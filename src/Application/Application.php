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
use Bluz\Config\ConfigException;
use Bluz\Config\ConfigLoader;
use Bluz\Http\Exception\ForbiddenException;
use Bluz\Http\Exception\RedirectException;
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
use Bluz\Response\ContentType;
use Exception;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use Laminas\Diactoros\ServerRequest;

/**
 * Application
 *
 * @package  Bluz\Application
 * @link     https://github.com/bluzphp/framework/wiki/Application
 * @author   Anton Shevchuk
 * @created  06.07.11 16:25
 *
 * @method Controller error(Exception $exception)
 * @method mixed forbidden(ForbiddenException $exception)
 * @method null redirect(RedirectException $url)
 */
class Application
{
    use Common\Helper;
    use Common\Singleton;

    /**
     * @var string Environment name
     */
    protected string $environment = 'production';

    /**
     * @var string|null Application path
     */
    protected ?string $path = null;

    /**
     * @var bool Debug application flag
     */
    protected bool $debugFlag = false;

    /**
     * @var bool Layout usage flag
     */
    protected bool $layoutFlag = true;

    /**
     * Get application environment
     *
     * @return string
     */
    public function getEnvironment(): string
    {
        return $this->environment;
    }

    /**
     * Get path to Application
     *
     * @return string
     * @throws ApplicationException
     */
    public function getPath(): string
    {
        if (!$this->path) {
            $this->setPath(
                $this->detectPath()
            );
        }
        return $this->path;
    }

    /**
     * @param string $environment
     */
    public function setEnvironment(string $environment): void
    {
        $this->environment = $environment;
    }

    /**
     * @param string $path
     * @throws ApplicationException
     */
    public function setPath(string $path): void
    {
        if (!is_readable($path)) {
            throw new ApplicationException('Application path is not readable');
        }
        $this->path = $path;
    }

    /**
     * Try to detect path of the Application
     * @return string
     */
    protected function detectPath(): string
    {
        $reflection = new ReflectionClass($this);
        return dirname($reflection->getFileName(), 3); // 3 level up
    }

    /**
     * Return Debug flag
     *
     * @return bool
     */
    public function isDebug(): bool
    {
        return $this->debugFlag;
    }

    /**
     * Return/setup Layout Flag
     *
     * @param bool|null $flag
     *
     * @return bool
     */
    public function useLayout(?bool $flag = null): bool
    {
        if (is_bool($flag)) {
            $this->layoutFlag = $flag;
        }

        return $this->layoutFlag;
    }

    /**
     * Initialize system packages
     *
     * @return void
     * @throws ApplicationException
     */
    public function init(): void
    {
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

            // init Request
            $this->initRequest();

            // init Response
            $this->initResponse();

            // init Translator
            $this->initTranslator();

            // init Router
            $this->initRouter();
        } catch (Exception $e) {
            throw new ApplicationException("Application can't be loaded: " . $e->getMessage());
        }
    }

    /**
     * Initial Request instance
     *
     * @return void
     * @throws ConfigException
     * @throws ApplicationException
     */
    protected function initConfig(): void
    {
        $loader = new ConfigLoader();
        // load default configuration
        $loader->load($this->getPath() . '/configs/default');
        // and merge it with environment configuration
        $loader->load($this->getPath() . '/configs/' . $this->getEnvironment());

        $config = new \Bluz\Config\Config();
        $config->setFromArray($loader->getConfig());

        Config::setInstance($config);

        // setup configuration for current environment
        if ($debug = Config::get('debug')) {
            $this->debugFlag = (bool)$debug;
        }

        // initial php settings
        if ($ini = Config::get('php')) {
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
     * @throws InvalidArgumentException
     */
    protected function initRequest(): void
    {
        $request = RequestFactory::fromGlobals();

        Request::setInstance($request);
    }

    /**
     * Initial Response instance
     *
     * @return void
     */
    protected function initResponse(): void
    {
        $response = new ResponseInstance();

        Response::setInstance($response);
    }

    /**
     * Get Response instance
     *
     * @return ResponseInstance
     */
    public function getResponse(): ResponseInstance
    {
        return Response::getInstance();
    }

    /**
     * Get Request instance
     *
     * @return ServerRequest
     */
    public function getRequest(): ServerRequest
    {
        return Request::getInstance();
    }

    /**
     * Initial Router instance
     *
     * @return void
     */
    protected function initRouter(): void
    {
        $router = new \Bluz\Router\Router();
        $router->setOptions(Config::get('router'));

        Router::setInstance($router);
    }

    /**
     * Initial Translator instance
     *
     * @return void
     * @throws Common\Exception\ConfigurationException
     */
    protected function initTranslator(): void
    {
        $translator = new \Bluz\Translator\Translator();
        $translator->setOptions(Config::get('translator'));
        $translator->init();

        Translator::setInstance($translator);
    }

    /**
     * Run application
     *
     * @return void
     */
    public function run(): void
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
     */
    public function process(): void
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
     */
    protected function preProcess(): void
    {
        Router::process();

        // disable Layout for XmlHttpRequests
        if (Request::isXmlHttpRequest()) {
            $this->layoutFlag = false;
        }

        // switch to JSON response based on Accept header
        if (Request::checkAccept([Request::TYPE_HTML, Request::TYPE_JSON]) === Request::TYPE_JSON) {
            $this->layoutFlag = false;
            Response::setContentType(ContentType::JSON);
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
    protected function doProcess(): void
    {
        $module = Request::getModule();
        $controller = Request::getController();
        $params = Request::getParams();

        try {
            // try to dispatch controller
            $result = $this->dispatch($module, $controller, $params);
        } catch (ForbiddenException $e) {
            // dispatch default error controller
            $result = $this->forbidden($e);
        } catch (RedirectException $e) {
            // should return `null` for disable output and setup redirect headers
            $result = $this->redirect($e);
        } catch (Exception $e) {
            // dispatch default error controller
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
    protected function postProcess(): void
    {
        // nothing
    }

    /**
     * Dispatch controller with params
     *
     * Call dispatch from any \Bluz\Package
     *     Application::getInstance()->dispatch($module, $controller, array $params);
     *
     * @param string $module
     * @param string $controller
     * @param array $params
     *
     * @return Controller
     * @throws CommonException
     * @throws ComponentException
     * @throws ControllerException
     * @throws ReflectionException
     */
    public function dispatch(string $module, string $controller, array $params = []): Controller
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
     * @param Controller $controller
     *
     * @return void
     */
    protected function preDispatch(Controller $controller): void
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
     * @param Controller $controller
     *
     * @return void
     * @throws ComponentException
     * @throws ControllerException
     * @throws ReflectionException
     */
    protected function doDispatch(Controller $controller): void
    {
        // run controller
        $controller->run();
    }

    /**
     * Extension point: post dispatch
     *
     * @param Controller $controller
     *
     * @return void
     */
    protected function postDispatch(Controller $controller): void
    {
        // nothing by default
    }

    /**
     * Render send Response
     *
     * @return void
     */
    public function render(): void
    {
        Response::send();
    }

    /**
     * Extension point: finally method
     *
     * @return void
     */
    public function end(): void
    {
        // nothing by default
    }
}
