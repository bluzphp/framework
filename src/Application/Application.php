<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Application;

use Bluz\Application\Exception\ApplicationException;
use Bluz\Application\Exception\ForbiddenException;
use Bluz\Application\Exception\RedirectException;
use Bluz\Common;
use Bluz\Controller\Controller;
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

/**
 * Application
 *
 * @package  Bluz\Application
 * @link     https://github.com/bluzphp/framework/wiki/Application
 * @author   Anton Shevchuk
 * @created  06.07.11 16:25
 *
 * @method Controller error(\Exception $exception)
 * @method Controller forbidden(ForbiddenException $exception)
 * @method null redirect(string $url)
 * @method null reload()
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
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * Get path to Application
     *
     * @return string
     */
    public function getPath()
    {
        if (!$this->path) {
            if (defined('PATH_APPLICATION')) {
                $this->path = PATH_APPLICATION;
            } else {
                $reflection = new \ReflectionClass($this);
                // 3 level up
                $this->path = dirname(dirname(dirname($reflection->getFileName())));
            }
        }
        return $this->path;
    }

    /**
     * Return Debug flag
     *
     * @return bool
     */
    public function isDebug()
    {
        return $this->debugFlag;
    }

    /**
     * Return Layout Flag
     *
     * @param  bool|null $flag
     * @return bool
     */
    public function useLayout($flag = null)
    {
        if (is_bool($flag)) {
            $this->layoutFlag = $flag;
        }
        
        return $this->layoutFlag;
    }

    /**
     * Initialize process
     *
     * @param  string $environment
     * @throws ApplicationException
     * @return void
     */
    public function init($environment = 'production')
    {
        $this->environment = $environment;

        try {
            // first log message
            Logger::info('app:init');

            // initial default helper path
            $this->addHelperPath(dirname(__FILE__) . '/Helper/');

            // setup configuration for current environment
            if ($debug = Config::getData('debug')) {
                $this->debugFlag = (bool) $debug;
            }

            // initial php settings
            if ($ini = Config::getData('php')) {
                foreach ($ini as $key => $value) {
                    $result = ini_set($key, $value);
                    Logger::info('app:init:php:'.$key.':'.($result?:'---'));
                }
            }

            // init session, start inside class
            Session::getInstance();

            // init Messages
            Messages::getInstance();

            // init Translator
            Translator::getInstance();

            // init request
            $this->initRequest();

            // init response
            $this->initResponse();

            // init router
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
    public function getResponse()
    {
        return Response::getInstance();
    }

    /**
     * Get Request instance
     *
     * @return \Zend\Diactoros\ServerRequest
     */
    public function getRequest()
    {
        return Request::getInstance();
    }

    /**
     * Run application
     *
     * @return void
     */
    public function run()
    {
        $this->process();
        $this->render();
        $this->finish();
    }

    /**
     * Process application
     *
     * Note:
     * - Why you don't use "X-" prefix for custom headers?
     * - Because it deprecated ({@link http://tools.ietf.org/html/rfc6648})
     *
     * @return void
     */
    public function process()
    {
        Logger::info('app:process');

        $this->preProcess();
        $this->doProcess();
        $this->postProcess();
    }

    /**
     * Pre process
     *
     * @return void
     * @throws ApplicationException
     */
    protected function preProcess()
    {
        Logger::info("app:process:pre");

        Router::process();

        if (Request::isXmlHttpRequest()
            or Request::getAccept([Request::TYPE_HTML, Request::TYPE_JSON]) == Request::TYPE_JSON
        ) {
            $this->layoutFlag = false;
        }
    }

    /**
     * Do process
     *
     * @return void
     */
    protected function doProcess()
    {
        Logger::info("app:process:do");

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
     * Post process
     *
     * @return void
     */
    protected function postProcess()
    {
        Logger::info("app:process:post");
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
     * @return Controller
     * @throws ApplicationException
     */
    public function dispatch($module, $controller, $params = array())
    {
        Logger::info("app:dispatch: " . $module . '/' . $controller);

        $this->preDispatch($module, $controller, $params);
        $result = $this->doDispatch($module, $controller, $params);
        $this->postDispatch($module, $controller, $params);

        return $result;
    }

    /**
     * Pre dispatch mount point
     *
     * @param  string $module
     * @param  string $controller
     * @param  array  $params
     * @return void
     */
    protected function preDispatch($module, $controller, $params = array())
    {
        Logger::info("---:dispatch:pre: " . $module . '/' . $controller);
    }

    /**
     * Do dispatch
     *
     * @param  string $module
     * @param  string $controller
     * @param  array  $params
     * @return Controller
     */
    protected function doDispatch($module, $controller, $params = array())
    {
        // @TODO: try to find custom controller class

        // create controller controller
        $controllerInstance = new Controller($module, $controller);

        // check HTTP Accept header
        $controllerInstance->checkAccept();
        // check HTTP method
        $controllerInstance->checkMethod();
        // check ACL privileges
        $controllerInstance->checkPrivilege();

        // run controller
        $controllerInstance->run($params);

        return $controllerInstance;
    }

    /**
     * Post dispatch mount point
     *
     * @param  string $module
     * @param  string $controller
     * @param  array  $params
     * @return void
     */
    protected function postDispatch($module, $controller, $params = array())
    {
        Logger::info("---:dispatch:post: " . $module . '/' . $controller);
    }

    /**
     * Render, is send Response
     *
     * @return void
     */
    public function render()
    {
        Logger::info('app:render');

        Response::send();
    }

    /**
     * Finally method
     *
     * @return void
     */
    public function finish()
    {
        Logger::info('app:finish');
    }
}
