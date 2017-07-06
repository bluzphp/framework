<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Application;

use Bluz\Application\Exception\ApplicationException;
use Bluz\Application\Exception\ForbiddenException;
use Bluz\Application\Exception\NotAcceptableException;
use Bluz\Application\Exception\NotAllowedException;
use Bluz\Application\Exception\RedirectException;
use Bluz\Common;
use Bluz\Common\Exception\CommonException;
use Bluz\Common\Exception\ComponentException;
use Bluz\Controller\Controller;
use Bluz\Controller\ControllerException;
use Bluz\Proxy\Config;
use Bluz\Proxy\Layout;
use Bluz\Proxy\Logger;
use Bluz\Proxy\Messages;
use Bluz\Proxy\Request;
use Bluz\Proxy\Response;
use Bluz\Proxy\Router;
use Bluz\Proxy\Session;
use Bluz\Proxy\Translator;
use Bluz\Request\RequestFactory;
use Bluz\Response\Response as ResponseInstance;
use Zend\Diactoros\ServerRequest;

/**
 * Application
 *
 * @package  Bluz\Application
 * @link     https://github.com/bluzphp/framework/wiki/Application
 * @author   Anton Shevchuk
 * @created  06.07.11 16:25
 *
 * @method Controller error(\Exception $exception)
 * @method mixed forbidden(ForbiddenException $exception)
 * @method null redirect(string $url)
 */
class Application
{
    use Common\Helper;
    use Common\Singleton;

    /**
     * @var string Environment name
     */
    protected $environment = 'production';

    /**
     * @var string Application path
     */
    protected $path;

    /**
     * @var bool Debug application flag
     */
    protected $debugFlag = false;

    /**
     * @var bool Layout usage flag
     */
    protected $layoutFlag = true;

    /**
     * Get application environment
     *
     * @return string
     */
    public function getEnvironment() : string
    {
        return $this->environment;
    }

    /**
     * Get path to Application
     *
     * @return string
     */
    public function getPath() : string
    {
        if (!$this->path) {
            if (defined('PATH_APPLICATION')) {
                $this->path = PATH_APPLICATION;
            } else {
                $reflection = new \ReflectionClass($this);
                // 3 level up
                $this->path = dirname($reflection->getFileName(), 3);
            }
        }
        return $this->path;
    }

    /**
     * Return Debug flag
     *
     * @return bool
     */
    public function isDebug() : bool
    {
        return $this->debugFlag;
    }

    /**
     * Return/setup Layout Flag
     *
     * @param  bool|null $flag
     *
     * @return bool
     */
    public function useLayout($flag = null) : bool
    {
        if (is_bool($flag)) {
            $this->layoutFlag = $flag;
        }

        return $this->layoutFlag;
    }

    /**
     * Initialize system packages
     *
     * @param  string $environment
     *
     * @throws ApplicationException
     * @return void
     */
    public function init($environment = 'production')
    {
        $this->environment = $environment;

        try {
            // initial default helper path
            $this->addHelperPath(__DIR__ . '/Helper/');

            // init Config
            $this->initConfig();

            // first log message
            Logger::info('app:init');

            // init Session, start inside class (if needed)
            Session::getInstance();

            // init Messages
            Messages::getInstance();

            // init Translator
            Translator::getInstance();

            // init Request
            $this->initRequest();

            // init Response
            $this->initResponse();

            // init Router
            $this->initRouter();
        } catch (\Exception $e) {
            throw new ApplicationException("Application can't be loaded: " . $e->getMessage());
        }
    }

    /**
     * Initial Request instance
     *
     * @return void
     */
    protected function initConfig()
    {
        Config::getInstance();

        // setup configuration for current environment
        if ($debug = Config::getData('debug')) {
            $this->debugFlag = (bool)$debug;
        }

        // initial php settings
        if ($ini = Config::getData('php')) {
            foreach ($ini as $key => $value) {
                $result = ini_set($key, $value);
                Logger::info('app:init:php:' . $key . ':' . ($result ?: '---'));
            }
        }
    }

    /**
     * Initial Request instance
     *
     * @return void
     * @throws \InvalidArgumentException
     */
    protected function initRequest()
    {
        $request = RequestFactory::fromGlobals();

        Request::setInstance($request);
    }

    /**
     * Initial Response instance
     *
     * @return void
     */
    protected function initResponse()
    {
        $response = new ResponseInstance();

        Response::setInstance($response);
    }

    /**
     * Initial Router instance
     *
     * @return void
     */
    protected function initRouter()
    {
        $router = new \Bluz\Router\Router();
        $router->setOptions(Config::getData('router'));

        Router::setInstance($router);
    }

    /**
     * Get Response instance
     *
     * @return \Bluz\Response\Response
     */
    public function getResponse() : ResponseInstance
    {
        return Response::getInstance();
    }

    /**
     * Get Request instance
     *
     * @return \Zend\Diactoros\ServerRequest
     */
    public function getRequest() : ServerRequest
    {
        return Request::getInstance();
    }

    /**
     * Run application
     *
     * @return void
     * @throws ApplicationException
     */
    public function run()
    {
        $this->process();
        $this->render();
        $this->end();
    }

    /**
     * Process application
     *
     * Note:
     * - Why you don't use "X-" prefix for custom headers?
     * - Because it deprecated ({@link http://tools.ietf.org/html/rfc6648})
     *
     * @return void
     * @throws ApplicationException
     */
    public function process()
    {
        $this->preProcess();
        $this->doProcess();
        $this->postProcess();
    }

    /**
     * Extension point: pre process
     *
     * - Router processing
     * - Analyse request headers
     *
     * @return void
     * @throws ApplicationException
     */
    protected function preProcess()
    {
        Router::process();

        // disable Layout for XmlHttpRequests
        if (Request::isXmlHttpRequest()) {
            $this->layoutFlag = false;
        }

        // switch to JSON response based on Accept header
        if (Request::checkAccept([Request::TYPE_HTML, Request::TYPE_JSON]) === Request::TYPE_JSON) {
            $this->layoutFlag = false;
            Response::switchType('JSON');
        }
    }

    /**
     * Do process
     *
     * - Dispatch controller
     * - Exceptions handling
     * - Setup layout
     * - Setup response body
     *
     * @return void
     */
    protected function doProcess()
    {
        $module = Request::getModule();
        $controller = Request::getController();
        $params = Request::getParams();

        // try to dispatch controller
        try {
            // dispatch controller
            $result = $this->dispatch($module, $controller, $params);
        } catch (ForbiddenException $e) {
            $result = $this->forbidden($e);
        } catch (RedirectException $e) {
            // redirect to URL
            $result = $this->redirect($e->getUrl());
        } catch (\Exception $e) {
            $result = $this->error($e);
        }

        // setup layout, if needed
        if ($this->useLayout()) {
            // render view to layout
            // needed for headScript and headStyle helpers
            Layout::setContent($result->render());
            Response::setBody(Layout::getInstance());
        } else {
            Response::setBody($result);
        }
    }

    /**
     * Extension point: post process
     *
     * @return void
     */
    protected function postProcess()
    {
        // nothing
    }

    /**
     * Dispatch controller with params
     *
     * Call dispatch from any \Bluz\Package
     *     Application::getInstance()->dispatch($module, $controller, array $params);
     *
     * @param  string $module
     * @param  string $controller
     * @param  array  $params
     *
     * @return Controller
     * @throws ComponentException
     * @throws CommonException
     * @throws ControllerException
     * @throws ForbiddenException
     * @throws NotAcceptableException
     * @throws NotAllowedException
     */
    public function dispatch($module, $controller, array $params = [])
    {
        $instance = new Controller($module, $controller, $params);

        Logger::info("app:dispatch:>>>: $module/$controller");
        $this->preDispatch($instance);

        Logger::info("app:dispatch:===: $module/$controller");
        $this->doDispatch($instance);

        Logger::info("app:dispatch:<<<: $module/$controller");
        $this->postDispatch($instance);

        return $instance;
    }

    /**
     * Extension point: pre dispatch
     *
     * @param  Controller $controller
     *
     * @return void
     * @throws ComponentException
     * @throws ForbiddenException
     * @throws NotAcceptableException
     * @throws NotAllowedException
     */
    protected function preDispatch($controller)
    {
        // check HTTP method
        $controller->checkHttpMethod();

        // check ACL privileges
        $controller->checkPrivilege();

        // check HTTP Accept header
        $controller->checkHttpAccept();
    }

    /**
     * Do dispatch
     *
     * @param  Controller $controller
     *
     * @return void
     * @throws ComponentException
     * @throws ControllerException
     */
    protected function doDispatch($controller)
    {
        // run controller
        $controller->run();
    }

    /**
     * Extension point: post dispatch
     *
     * @param  Controller $controller
     *
     * @return void
     */
    protected function postDispatch($controller)
    {
        // nothing by default
    }

    /**
     * Render, is send Response
     *
     * @return void
     */
    public function render()
    {
        Response::send();
    }

    /**
     * Extension point: finally method
     *
     * @return void
     */
    public function end()
    {
        // nothing
    }
}
