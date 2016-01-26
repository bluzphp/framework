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
use Bluz\Application\Exception\NotAcceptableException;
use Bluz\Application\Exception\NotAllowedException;
use Bluz\Application\Exception\RedirectException;
use Bluz\Application\Exception\ReloadException;
use Bluz\Auth\AbstractRowEntity;
use Bluz\Common;
use Bluz\Controller\Reflection;
use Bluz\Http;
use Bluz\Proxy\Acl;
use Bluz\Proxy\Cache;
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
use Bluz\View\View;

/**
 * Application
 *
 * @package  Bluz\Application
 * @link     https://github.com/bluzphp/framework/wiki/Application *
 * @author   Anton Shevchuk
 * @created  06.07.11 16:25
 *
 * @method void denied()
 * @method void redirect(string $url)
 * @method void redirectTo(string $module, string $controller, array $params = array())
 * @method void reload()
 * @method AbstractRowEntity user()
 */
class Application
{
    use Common\Helper;
    use Common\Singleton;

    /**
     * @var string Application path
     */
    protected $path;

    /**
     * @var string Environment name
     */
    protected $environment = 'production';

    /**
     * @var \Exception
     */
    protected $exception;

    /**
     * @var bool Debug application flag
     */
    protected $debugFlag = false;

    /**
     * @var bool Layout flag
     */
    protected $layoutFlag = true;

    /**
     * @var array Stack of widgets closures
     */
    protected $widgets = array();

    /**
     * @var array Stack of API closures
     */
    protected $api = array();

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
     * Check debug flag
     *
     * @return bool
     */
    public function isDebug()
    {
        return $this->debugFlag;
    }

    /**
     * Check Layout flag
     *
     * @return bool
     */
    public function hasLayout()
    {
        return $this->layoutFlag;
    }

    /**
     * Set Layout template and/or flag
     *
     * @param  bool|string $flag
     * @return void
     */
    public function useLayout($flag = true)
    {
        if (is_string($flag)) {
            Layout::setTemplate($flag);
            $this->layoutFlag = true;
        } else {
            $this->layoutFlag = $flag;
        }
    }

    /**
     * Set JSON presentation
     *
     * @return void
     */
    public function useJson()
    {
        // disable view and layout for JSON output
        $this->useLayout(false);
        Response::setHeader('Content-Type', 'application/json');
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
            // initial default helper path
            $this->addHelperPath(dirname(__FILE__) . '/Helper/');

            // first log message
            Logger::info('app:init');

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
     * Initial controller view
     *
     * @param  string $module
     * @param  string $controller
     * @return View
     */
    protected function initView($module, $controller)
    {
        // $view for use in closure
        $view = new View();

        // setup additional helper path
        $view->addHelperPath($this->getPath() . '/layouts/helpers');

        // setup additional partial path
        $view->addPartialPath($this->getPath() . '/layouts/partial');

        // setup default path
        $view->setPath($this->getPath() . '/modules/' . $module . '/views');

        // setup default template
        $view->setTemplate($controller . '.phtml');

        return $view;
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

        // disable layout for AJAX requests
        if (Request::isXmlHttpRequest()) {
            $this->useLayout(false);
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
            // get reflection of requested controller
            $controllerFile = $this->getControllerFile($module, $controller);
            $reflection = $this->reflection($controllerFile);

            // check header "accept" for catch JSON requests, and switch response to JSON
            // it's some magic for AJAX and REST requests
            $acceptMap = [
                'HTML' => 'text/html',
                'JSON' => 'application/json'
            ];

            // some controller has @accept tag
            if ($allowAccept = $reflection->getAccept()) {
                // convert list of controller @accept to MIME types
                $allowAccept = array_filter(
                    $acceptMap,
                    function ($key) use ($allowAccept) {
                        return in_array($key, $allowAccept);
                    },
                    ARRAY_FILTER_USE_KEY
                );
                $allowAccept = array_values($allowAccept);
            } else {
                // by default allow just HTML output
                $allowAccept = ['text/html'];
            }

            // choose MIME type by browser accept header
            // filtered by controller @accept
            $accept = Request::getAccept($allowAccept);

            // switch statement for Accept header
            switch ($accept) {
                case 'text/html':
                    // HTML response with layout
                    break;
                case 'application/json':
                    $this->useJson();
                    break;
                default:
                    if (PHP_SAPI == 'cli') {
                        // all ok
                    } else {
                        // not acceptable MIME type
                        throw new NotAcceptableException();
                    }
            }

            // check call method(s)
            if ($reflection->getMethod() && !in_array(Request::getMethod(), $reflection->getMethod())) {
                throw new NotAllowedException(join(',', $reflection->getMethod()));
            }

            // check HTML cache
            if ($reflection->getCacheHtml() && Request::getMethod() == Request::METHOD_GET) {
                $htmlKey = 'html:' . $module . ':' . $controller . ':' . http_build_query($params);
                if ($cachedHtml = Cache::get($htmlKey)) {
                    Response::setBody($cachedHtml);
                    return;
                }
            }

            // dispatch controller
            $dispatchResult = $this->dispatch($module, $controller, $params);

        } catch (RedirectException $e) {
            Response::removeHeaders();
            Response::clearBody();

            if (Request::isXmlHttpRequest()) {
                Response::setStatusCode(204);
                Response::setHeader('Bluz-Redirect', $e->getMessage());
                return;
            } else {
                Response::setStatusCode(302);
                Response::setHeader('Location', $e->getMessage());
                return;
            }
        } catch (ReloadException $e) {
            Response::removeHeaders();
            Response::clearBody();

            if (Request::isXmlHttpRequest()) {
                Response::setStatusCode(204);
                Response::setHeader('Bluz-Reload', 'true');
                return;
            } else {
                Response::setStatusCode(302);
                Response::setHeader('Location', Request::getRequestUri());
                return;
            }
        } catch (\Exception $e) {
            $this->setException($e);

            Response::removeHeaders();
            Response::clearBody();

            // cast to valid HTTP error code
            // 500 - Internal Server Error
            $statusCode = (100 <= $e->getCode() && $e->getCode() <= 505) ? $e->getCode() : 500;
            Response::setStatusCode($statusCode);

            // TODO: if `error` controller consist error
            $dispatchResult = $this->dispatch(
                Router::getErrorModule(),
                Router::getErrorController(),
                array(
                    'code' => $e->getCode(),
                    'message' => $e->getMessage()
                )
            );
        }

        if ($this->hasLayout()) {
            Layout::setContent($dispatchResult);
            $dispatchResult = Layout::getInstance();
        }

        if (isset($htmlKey, $reflection)) {
            // @TODO: Added ETag header
            Cache::set($htmlKey, $dispatchResult(), $reflection->getCacheHtml());
            Cache::addTag($htmlKey, $module);
            Cache::addTag($htmlKey, 'html');
            Cache::addTag($htmlKey, 'html:' . $module);
            Cache::addTag($htmlKey, 'html:' . $module . ':' . $controller);
        }
        Response::setBody($dispatchResult);
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
     * @return View|callable
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

        // check privilege before run controller
        if (!$this->isAllowed($module, $controller)) {
            $this->denied();
        }
    }

    /**
     * Do dispatch
     *
     * @param  string $module
     * @param  string $controller
     * @param  array  $params
     * @return View|callable
     * @throws ApplicationException
     */
    protected function doDispatch($module, $controller, $params = array())
    {
        Logger::info("---:dispatch:do: " . $module . '/' . $controller);
        $controllerFile = $this->getControllerFile($module, $controller);
        $reflection = $this->reflection($controllerFile);

        if ($reflection->getCache()) {
            $cacheKey = 'view:' . $module . ':' . $controller . ':' . http_build_query($params);
            if ($cachedView = Cache::get($cacheKey)) {
                return $cachedView;
            }
        }

        // process params
        $params = $reflection->params($params);

        $view = $this->initView($module, $controller);

        $bootstrapPath = $this->getPath() . '/modules/' . $module . '/bootstrap.php';

        /**
         * optional $bootstrap for use in closure
         * @var \closure $bootstrap
         */
        if (file_exists($bootstrapPath)) {
            $bootstrap = require $bootstrapPath;
        } else {
            $bootstrap = null;
        }
        unset($bootstrapPath);

        /**
         * @var \closure $controllerClosure
         */
        $controllerClosure = include $controllerFile;

        if (!is_callable($controllerClosure)) {
            throw new ApplicationException("Controller is not callable '$module/$controller'");
        }

        $result = $controllerClosure(...$params);

        // switch statement for result of Closure run
        switch (true) {
            case ($result === false):
                // return "false" is equal to disable view and layout
                $this->useLayout(false);
                return function () {
                    // just empty closure
                };
            case is_callable($result):
            case is_object($result):
                // return callable structure (closure or object) for replace logic of controller
                // or return any class
                return $result;
            case is_string($result):
                // return string variable is equal to change view template
                $view->setTemplate($result);
                break;
            case is_array($result):
                // return associative array is equal to setup view data
                $view->setFromArray($result);
                break;
        }

        if (isset($cacheKey)) {
            Cache::set($cacheKey, $view, $reflection->getCache());
            Cache::addTag($cacheKey, $module);
            Cache::addTag($cacheKey, 'view');
            Cache::addTag($cacheKey, 'view:' . $module);
            Cache::addTag($cacheKey, 'view:' . $module . ':' . $controller);
        }

        return $view;
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
     * @return \Bluz\Request\RequestFactory
     */
    public function getRequest()
    {
        return Request::getInstance();
    }

    /**
     * setException
     *
     * @param \Exception $exception
     * @return void
     */
    public function setException($exception)
    {
        $this->exception = $exception;
    }

    /**
     * getException
     *
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * Widget call
     *
     * Call widget from any \Bluz\Package
     * <code>
     *     Application::getInstance()->widget($module, $widget, array $params);
     * </code>
     *
     * @param  string $module
     * @param  string $widget
     * @param  array  $params
     * @return \Closure
     * @throws ApplicationException
     */
    public function widget($module, $widget, $params = array())
    {
        Logger::info("app:widget: " . $module . '/' . $widget);
        $widgetFile = $this->getWidgetFile($module, $widget);
        $reflection = $this->reflection($widgetFile);

        // check acl
        if (!Acl::isAllowed($module, $reflection->getPrivilege())) {
            throw new ForbiddenException("Not enough permissions for call widget '$module/$widget'");
        }

        /**
         * Cachable widgets
         * @var \Closure $widgetClosure
         */
        if (isset($this->widgets[$module], $this->widgets[$module][$widget])) {
            $widgetClosure = $this->widgets[$module][$widget];
        } else {
            $widgetClosure = include $widgetFile;

            if (!isset($this->widgets[$module])) {
                $this->widgets[$module] = array();
            }
            $this->widgets[$module][$widget] = $widgetClosure;
        }

        if (!is_callable($widgetClosure)) {
            throw new ApplicationException("Widget is not callable '$module/$widget'");
        }

        return $widgetClosure;
    }

    /**
     * Api call
     *
     * Call API from any \Bluz\Package
     * <code>
     *     Application::getInstance()->api($module, $widget, array $params);
     * </code>
     *
     * @param  string $module
     * @param  string $method
     * @return \Closure
     * @throws ApplicationException
     */
    public function api($module, $method)
    {
        Logger::info("app:api: " . $module . '/' . $method);

        /**
         * Cachable APIs
         * @var \Closure $widgetClosure
         */
        if (isset($this->api[$module])
            && isset($this->api[$module][$method])
        ) {
            $apiClosure = $this->api[$module][$method];
        } else {
            $apiClosure = require $this->getApiFile($module, $method);

            if (!isset($this->api[$module])) {
                $this->api[$module] = array();
            }
            $this->api[$module][$method] = $apiClosure;
        }

        if (!is_callable($apiClosure)) {
            throw new ApplicationException("API is not callable '$module/$method'");
        }

        return $apiClosure;
    }

    /**
     * Retrieve reflection for anonymous function
     *
     * @param  string $file
     * @return Reflection
     * @throws ApplicationException
     */
    public function reflection($file)
    {
        // cache for reflection data
        if (!$reflection = Cache::get('reflection:' . $file)) {
            $reflection = new Reflection($file);
            $reflection->process();

            Cache::set('reflection:' . $file, $reflection);
            Cache::addTag('reflection:' . $file, 'reflection');
        }
        return $reflection;
    }

    /**
     * Is allowed controller/widget/etc
     *
     * @param  string $module
     * @param  string $controller
     * @return bool
     * @throws ApplicationException
     */
    public function isAllowed($module, $controller)
    {
        $file = $this->getControllerFile($module, $controller);
        $reflection = $this->reflection($file);

        if ($privilege = $reflection->getPrivilege()) {
            return Acl::isAllowed($module, $privilege);
        }
        return true;
    }

    /**
     * Get controller file
     *
     * @param  string $module
     * @param  string $controller
     * @return string
     * @throws ApplicationException
     */
    protected function getControllerFile($module, $controller)
    {
        $controllerPath = $this->getPath() . '/modules/' . $module
            . '/controllers/' . $controller . '.php';

        if (!file_exists($controllerPath)) {
            throw new ApplicationException("Controller file not found '$module/$controller'", 404);
        }

        return $controllerPath;
    }

    /**
     * Get widget file
     *
     * @param  string $module
     * @param  string $widget
     * @return string
     * @throws ApplicationException
     */
    protected function getWidgetFile($module, $widget)
    {
        $widgetPath = $this->getPath() . '/modules/' . $module
            . '/widgets/' . $widget . '.php';

        if (!file_exists($widgetPath)) {
            throw new ApplicationException("Widget file not found '$module/$widget'");
        }

        return $widgetPath;
    }

    /**
     * Get API file
     *
     * @param  string $module
     * @param  string $method
     * @return string
     * @throws ApplicationException
     */
    protected function getApiFile($module, $method)
    {
        $apiPath = $this->getPath() . '/modules/' . $module
            . '/api/' . $method . '.php';

        if (!file_exists($apiPath)) {
            throw new ApplicationException("API file not found '$module/$method'");
        }

        return $apiPath;
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
