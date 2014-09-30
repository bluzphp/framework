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
use Bluz\Application\Exception\ReloadException;
use Bluz\Auth\AbstractRowEntity;
use Bluz\Common;
use Bluz\Http;
use Bluz\Proxy;
use Bluz\Proxy\Acl;
use Bluz\Proxy\Auth;
use Bluz\Proxy\Cache;
use Bluz\Proxy\Config;
use Bluz\Proxy\Db;
use Bluz\Proxy\EventManager;
use Bluz\Proxy\Logger;
use Bluz\Proxy\Mailer;
use Bluz\Proxy\Messages;
use Bluz\Proxy\Registry;
use Bluz\Proxy\Router;
use Bluz\Proxy\Session;
use Bluz\Proxy\Translator;
use Bluz\View\Layout;
use Bluz\View\View;

/**
 * Application
 *
 * @package  Bluz\Application
 *
 * @method void denied()
 * @method void redirect(string $url)
 * @method void redirectTo(string $module, string $controller, array $params = array())
 * @method void reload()
 * @method AbstractRowEntity user()
 *
 * @author   Anton Shevchuk
 * @created  06.07.11 16:25
 */
abstract class Application
{
    use Common\Helper;
    use Common\Singleton;

    /**
     * @var Layout instance
     */
    protected $layout;

    /**
     * @var Http\Request instance
     */
    protected $request;

    /**
     * @var Http\Response instance
     */
    protected $response;

    /**
     * @var Session instance
     */
    protected $session;

    /**
     * Application path
     * @var string
     */
    protected $path;

    /**
     * Environment name
     * @var string
     */
    protected $environment = 'production';

    /**
     * Debug application flag
     * @var bool
     */
    protected $debugFlag = false;

    /**
     * Layout flag
     * @var bool
     */
    protected $layoutFlag = true;

    /**
     * JSON response flag
     * @var bool
     */
    protected $jsonFlag = false;

    /**
     * Stack of widgets closures
     * @var array
     */
    protected $widgets = array();

    /**
     * Stack of API closures
     * @var array
     */
    protected $api = array();

    /**
     * Dispatched module name
     * @var string
     */
    protected $dispatchModule;

    /**
     * Dispatched controller name
     * @var string
     */
    protected $dispatchController;

    /**
     * init
     *
     * @param string $environment Array format only!
     * @throws ApplicationException
     * @return Application
     */
    public function init($environment = 'production')
    {
        $this->environment = $environment;

        try {
            // initial default helper path
            $this->addHelperPath(dirname(__FILE__) . '/Helper/');

            // first log message
            $this->log('app:init');

            // setup configuration for current environment
            if ($debug = Config::getData('debug')) {
                $this->debugFlag = (bool) $debug;
            }

            // initial php settings
            if ($ini = Config::getData('php')) {
                foreach ($ini as $key => $value) {
                    $result = ini_set($key, $value);
                    $this->log('app:init:php:'.$key.':'.($result?:'---'));
                }
            }

            // initial session, start inside class
            Session::getInstance();

            // initial Messages
            Messages::getInstance();

            // initial Translator
            Translator::getInstance();

        } catch (\Exception $e) {
            throw new ApplicationException("Application can't be loaded: " . $e->getMessage());
        }
        return $this;
    }

    /**
     * Log message, working with logger
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function log($message, array $context = [])
    {
        Logger::info($message, $context);
    }

    /**
     * Load configuration file
     *
     * @deprecated since version 0.5.1
     * @return \Bluz\Config\Config
     */
    public function getConfig()
    {
        return Config::getInstance();
    }

    /**
     * Get configuration for same section and subsection
     *
     * @deprecated since version 0.5.1
     * @param string|null $section of config
     * @param string|null $subsection of config
     * @return array|mixed
     */
    public function getConfigData($section = null, $subsection = null)
    {
        return Config::getData($section, $subsection);
    }

    /**
     * Get Acl instance
     *
     * @deprecated since version 0.5.1
     * @return Acl
     */
    public function getAcl()
    {
        return Acl::getInstance();
    }

    /**
     * Get Auth instance
     *
     * @deprecated since version 0.5.1
     * @return Auth
     */
    public function getAuth()
    {
        return Auth::getInstance();
    }

    /**
     * If enabled return configured Cache or Nil otherwise
     *
     * @deprecated since version 0.5.1
     * @return Cache instance or Nil
     */
    public function getCache()
    {
        return Cache::getInstance();
    }

    /**
     * Get Db Instance
     *
     * @deprecated since version 0.5.1
     * @return Db
     */
    public function getDb()
    {
        return Db::getInstance();
    }

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
     * Get EventManager instance
     *
     * @deprecated since version 0.5.1
     * @return EventManager
     */
    public function getEventManager()
    {
        return EventManager::getInstance();
    }

    /**
     * Get Layout instance
     *
     * @return Layout
     */
    public function getLayout()
    {
        if (!$this->layout) {
            $this->layout = new Layout();
            $this->layout->setOptions(Config::getData('layout'));
        }
        return $this->layout;
    }

    /**
     * Get logger instance
     *
     * @deprecated since version 0.5.1
     * @return Logger instance or Nil
     */
    public function getLogger()
    {
        return Logger::getInstance();
    }

    /**
     * Get Mailer instance
     *
     * @deprecated since version 0.5.1
     * @return Mailer
     */
    public function getMailer()
    {
        return Mailer::getInstance();
    }

    /**
     * Get Messages instance
     *
     * @deprecated since version 0.5.1
     * @return Messages
     */
    public function getMessages()
    {
        return Messages::getInstance();
    }

    /**
     * Check Messages
     *
     * @deprecated since version 0.5.1
     * @return bool
     */
    public function hasMessages()
    {
        return Messages::count();
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
                $this->path = dirname($reflection->getFileName());
            }
        }
        return $this->path;
    }

    /**
     * Get Registry instance
     *
     * @deprecated since version 0.5.1
     * @return Registry
     */
    public function getRegistry()
    {
        return Registry::getInstance();
    }

    /**
     * Get Request instance
     *
     * @return Http\Request
     */
    public function getRequest()
    {
        if (!$this->request) {
            $this->request = new Http\Request();
            $this->request->setOptions(Config::getData('request'));

            // disable layout for AJAX requests
            if ($this->request->isXmlHttpRequest()) {
                $this->useLayout(false);
            }

            // check header "accept" for catch JSON requests, and switch to JSON response
            // for AJAX and REST requests
            if ($accept = $this->getRequest()->getHeader('accept')) {
                // MIME type can be "application/json", "application/json; charset=utf-8" etc.
                $accept = str_replace(';', ',', $accept);
                $accept = explode(',', $accept);
                if (in_array("application/json", $accept)) {
                    $this->useJson(true);
                }
            }
        }
        return $this->request;
    }

    /**
     * Set Request instance
     *
     * @param Http\Request $request
     * @return Application
     */
    public function setRequest($request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Get Response instance
     *
     * @return Http\Response
     */
    public function getResponse()
    {
        if (!$this->response) {
            $this->response = new Http\Response();
            $this->response->setOptions(Config::getData('response'));
        }
        return $this->response;
    }

    /**
     * Set Response instance
     *
     * @param Http\Response $response
     * @return Application
     */
    public function setResponse($response)
    {
        $this->response = $response;
        return $this;
    }

    /**
     * Get Router instance
     *
     * @return Router
     */
    public function getRouter()
    {
        return Router::getInstance();
    }

    /**
     * Get Session instance
     *
     * @deprecated since version 0.5.1
     * @return Session
     */
    public function getSession()
    {
        return Session::getInstance();
    }

    /**
     * Get Translator instance
     *
     * @deprecated since version 0.5.1
     * @return Translator
     */
    public function getTranslator()
    {
        return Translator::getInstance();
    }

    /**
     * Create new instance of view and return it
     *
     * @return View
     */
    public function getView()
    {
        $view = new View();

        // setup default partial path
        $view->addPartialPath($this->getPath() . '/layouts/partial');

        return $view;
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
     * Check Json flag
     *
     * @return bool
     */
    public function isJson()
    {
        return $this->jsonFlag;
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
     * @param bool|string $flag
     * @return Application
     */
    public function useLayout($flag = true)
    {
        if (is_string($flag)) {
            $this->getLayout()->setTemplate($flag);
            $this->layoutFlag = true;
        } else {
            $this->layoutFlag = $flag;
        }
        return $this;
    }

    /**
     * Set Json flag
     *
     * @param bool $flag
     * @return Application
     */
    public function useJson($flag = true)
    {
        if ($flag) {
            // disable view and layout for JSON output
            $this->useLayout(false);
        }
        $this->jsonFlag = $flag;
        return $this;
    }

    /**
     * Process application
     *
     * Note:
     * - Why you don't use "X-" prefix?
     * - Because it deprecated
     * @link http://tools.ietf.org/html/rfc6648
     */
    public function process()
    {
        $this->log('app:process');

        // init request
        $request = $this->getRequest();

        // init router
        Router::getInstance();

        // init response
        $response = $this->getResponse();

        // try to dispatch controller
        try {
            $dispatchResult = $this->dispatch(
                $request->getModule(),
                $request->getController(),
                $request->getAllParams()
            );

            if ($this->hasLayout()) {
                $this->getLayout()->setContent($dispatchResult);
                $dispatchResult = $this->getLayout();
            }

            $response->setBody($dispatchResult);
        } catch (RedirectException $e) {
            $response->setException($e);

            if ($request->isXmlHttpRequest()) {
                $response->setStatusCode(204);
                $response->setHeader('Bluz-Redirect', $e->getMessage());
            } else {
                $response->setStatusCode($e->getCode());
                $response->setHeader('Location', $e->getMessage());
            }
        } catch (ReloadException $e) {
            $response->setException($e);

            if ($request->isXmlHttpRequest()) {
                $response->setStatusCode(204);
                $response->setHeader('Bluz-Reload', 'true');
            } else {
                $response->setStatusCode($e->getCode());
                $response->setHeader('Refresh', '0; url=' . $request->getRequestUri());
            }
        } catch (\Exception $e) {
            $response->setException($e);

            $dispatchResult = $this->dispatch(
                Router::getErrorModule(),
                Router::getErrorController(),
                array(
                    'code' => $e->getCode(),
                    'message' => $e->getMessage()
                )
            );

            if ($this->hasLayout()) {
                $this->getLayout()->setContent($dispatchResult);
                $dispatchResult = $this->getLayout();
            }

            $response->setStatusCode($e->getCode());
            $response->setBody($dispatchResult);
        }

        return $this->getResponse();
    }

    /**
     * Pre dispatch mount point
     *
     * @param string $module
     * @param string $controller
     * @param array $params
     * @return void
     */
    protected function preDispatch($module, $controller, $params = array())
    {
        $this->log("app:dispatch:pre: " . $module . '/' . $controller);
    }

    /**
     * Do dispatch
     *
     * @param string $module
     * @param string $controller
     * @param array $params
     * @throws ApplicationException
     *
     * @return View
     */
    protected function doDispatch($module, $controller, $params = array())
    {
        $this->log("app:dispatch:do: " . $module . '/' . $controller);
        $controllerFile = $this->getControllerFile($module, $controller);
        $reflectionData = $this->reflection($controllerFile);

        // check acl
        if (!$this->isAllowed($module, $reflectionData)) {
            $this->denied();
        }

        // check method(s)
        if (isset($reflectionData['method'])
            && !in_array($this->getRequest()->getMethod(), $reflectionData['method'])
        ) {
            throw new ApplicationException(join(',', $reflectionData['method']), 405);
        }

        // cache initialization
        if (isset($reflectionData['cache'])) {
            $cacheKey = $module . '/' . $controller . '/' . http_build_query($params);
            if ($cachedView = Cache::get($cacheKey)) {
                return $cachedView;
            }
        }

        // process params
        $params = $this->params($reflectionData, $params);

        // $view for use in closure
        $view = $this->getView();
        // setup default path
        $view->setPath($this->getPath() . '/modules/' . $module . '/views');
        // setup default template
        $view->setTemplate($controller . '.phtml');

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

        $result = call_user_func_array($controllerClosure, $params);

        // return false is equal to disable view and layout
        if ($result === false) {
            $this->useLayout(false);
            return $result;
        }

        // return closure is replace logic of controller
        // or return any class
        if (is_callable($result) or
            is_object($result)
        ) {
            return $result;
        }

        // return string is equal to change view template
        if (is_string($result)) {
            $view->setTemplate($result);
        }

        // return array is equal to setup view
        if (is_array($result)) {
            $view->setFromArray($result);
        }

        if (isset($reflectionData['cache'], $cacheKey)) {
            Cache::set($cacheKey, $view, intval($reflectionData['cache']) * 60);
            Cache::addTag($cacheKey, 'view');
            Cache::addTag($cacheKey, 'view:' . $module);
            Cache::addTag($cacheKey, 'view:' . $module . ':' . $controller);
        }

        return $view;
    }

    /**
     * Post dispatch mount point
     *
     * @param string $module
     * @param string $controller
     * @param array $params
     * @return void
     */
    protected function postDispatch($module, $controller, $params = array())
    {
        $this->log("app:dispatch:post: " . $module . '/' . $controller);
    }

    /**
     * Dispatch controller with params
     *
     * Call dispatch from any \Bluz\Package
     *     app()->dispatch($module, $controller, array $params);
     *
     * Attach callback function to event "dispatch"
     *     app()->getEventManager()->attach('dispatch', function($event) {
     *         $eventParams = $event->getParams();
     *         $app = $event->getTarget();
     *         \Bluz\Profiler::log('bootstrap:dispatch: '.$eventParams['module'].'/'.$eventParams['controller']);
     *     });
     *
     * @param string $module
     * @param string $controller
     * @param array $params
     * @throws ApplicationException
     * @return View
     */
    public function dispatch($module, $controller, $params = array())
    {
        $this->log("app:dispatch: " . $module . '/' . $controller);

        // system trigger "dispatch"
        EventManager::trigger(
            'dispatch',
            $this,
            array(
                'module' => $module,
                'controller' => $controller,
                'params' => $params
            )
        );

        $this->dispatchModule = $module;
        $this->dispatchController = $controller;

        $this->preDispatch($module, $controller, $params);
        $result = $this->doDispatch($module, $controller, $params);
        $this->postDispatch($module, $controller, $params);

        return $result;
    }

    /**
     * Render, is send Response
     *
     * @return void
     */
    public function render()
    {
        $this->log('app:render');

        if ($this->isJson()) {
            // setup messages
            if (Messages::count()) {
                $this->getResponse()->setHeader('Bluz-Notify', json_encode(Messages::popAll()));
            }

            // prepare body
            if ($body = $this->getResponse()->getBody()) {
                $body = json_encode($body);
                // prepare to JSON output
                $this->getResponse()->setBody($body);
                // override response code so javascript can process it
                $this->getResponse()->setHeader('Content-Type', 'application/json');
                // setup content length
                $this->getResponse()->setHeader('Content-Length', strlen($body));
            }
        }

        $this->getResponse()->send();
    }

    /**
     * Widget call
     *
     * Call widget from any \Bluz\Package
     *     app()->widget($module, $widget, array $params);
     *
     * Attach callback function to event "widget"
     *     app()->getEventManager()->attach('widget', function($event) {
     *         $eventParams = $event->getParams();
     *         $app = $event->getTarget();
     *         \Bluz\Profiler::log('bootstrap:dispatch: '.$eventParams['module'].'/'.$eventParams['widget']);
     *     });
     *
     * @param string $module
     * @param string $widget
     * @param array $params
     * @throws ApplicationException
     * @return \Closure
     */
    public function widget($module, $widget, $params = array())
    {
        $this->log("app:widget: " . $module . '/' . $widget);
        $widgetFile = $this->getWidgetFile($module, $widget);
        $reflectionData = $this->reflection($widgetFile);


        EventManager::trigger(
            'widget',
            $this,
            array(
                'module' => $module,
                'widget' => $widget,
                'params' => $params,
                'reflection' => $reflectionData
            )
        );

        // check acl
        if (!$this->isAllowed($module, $reflectionData)) {
            throw new ForbiddenException("Not enough permissions for call widget '$module/$widget'");
        }

        /**
         * Cachable widgets
         * @var \Closure $widgetClosure
         */
        if (isset($this->widgets[$module])
            && isset($this->widgets[$module][$widget])
        ) {
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
     *     app()->api($module, $widget, array $params);
     *
     * Attach callback function to event "api"
     *     app()->getEventManager()->attach('api', function($event) {
     *         $eventParams = $event->getParams();
     *         $app = $event->getTarget();
     *         \Bluz\Profiler::log('bootstrap:dispatch: '.$eventParams['module'].'/'.$eventParams['widget']);
     *     });
     *
     * @param string $module
     * @param string $method
     * @throws ApplicationException
     * @return \Closure
     */
    public function api($module, $method)
    {
        $this->log("app:api: " . $module . '/' . $method);

        EventManager::trigger(
            'api',
            $this,
            array(
                'module' => $module,
                'method' => $method
            )
        );

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
     * @param string $file
     * @throws ApplicationException
     * @return array
     */
    public function reflection($file)
    {
        // cache for reflection data
        if (!$data = Cache::get('reflection:' . $file)) {

            // TODO: workaround for get reflection of closure function
            $bootstrap = $view = $module = $controller = null;
            /** @var \Closure|object $closure */
            $closure = include $file;

            if (!is_callable($closure)) {
                throw new ApplicationException("There is no closure in file $file");
            }

            // init data
            $data = array(
                'params' => [],
                'values' => [],
            );

            if ('Closure' == get_class($closure)) {
                $reflection = new \ReflectionFunction($closure);
            } else {
                $reflection = new \ReflectionObject($closure);
            }

            // check and normalize params by doc comment
            $docComment = $reflection->getDocComment();

            // get all options by one regular expression
            if (preg_match_all('/\s*\*\s*\@([a-z0-9-_]+)\s+(.*).*/i', $docComment, $matches)) {
                foreach ($matches[1] as $i => $key) {
                    $data[$key][] = trim($matches[2][$i]);
                }
            }

            // parameters available for Closure only
            if ($reflection instanceof \ReflectionFunction) {
                // get params and convert it to simple array
                $reflectionParams = $reflection->getParameters();

                // prepare params data
                // setup param types
                $types = array();
                if (isset($data['param'])) {
                    foreach ($data['param'] as $param) {
                        if (strpos($param, '$') === false) {
                            continue;
                        }
                        list($type, $key) = preg_split('/\$/', $param);
                        $type = trim($type);
                        if (!empty($type)) {
                            $types[$key] = $type;
                        }
                    }
                }

                // setup params and optional params
                $params = array();
                $values = array();
                foreach ($reflectionParams as $param) {
                    $name = $param->getName();
                    $params[$name] = isset($types[$name]) ? $types[$name] : null;
                    if ($param->isOptional()) {
                        $values[$name] = $param->getDefaultValue();
                    }
                }
                $data['params'] = $params;
                $data['values'] = $values;
            }

            // prepare cache ttl settings
            if (isset($data['cache'])) {
                $cache = current($data['cache']);
                $num = (int)$cache;
                $time = substr($cache, strpos($cache, ' '));
                switch ($time) {
                    case 'day':
                    case 'days':
                        $data['cache'] = (int)$num * 60 * 24;
                        break;
                    case 'hour':
                    case 'hours':
                        $data['cache'] = (int)$num * 60;
                        break;
                    case 'min':
                    default:
                        $data['cache'] = (int)$num;
                }
            }

            // prepare acl settings
            // only one privilege
            if (isset($data['privilege'])) {
                $data['privilege'] = current($data['privilege']);
            }

            // clean unused data
            unset($data['return'], $data['param']);

            Cache::set('reflection:' . $file, $data);
            Cache::addTag('reflection:' . $file, 'reflection');
        }
        return $data;
    }

    /**
     * Process params
     *  - type conversion
     *  - default values
     *
     * @param array $reflectionData
     * @param array $rawData
     * @return array
     */
    private function params($reflectionData, $rawData)
    {
        // need use new array for order params as described in controller
        $params = array();
        foreach ($reflectionData['params'] as $param => $type) {
            if (isset($rawData[$param])) {
                switch ($type) {
                    case 'bool':
                    case 'boolean':
                        $params[] = (bool)$rawData[$param];
                        break;
                    case 'int':
                    case 'integer':
                        $params[] = (int)$rawData[$param];
                        break;
                    case 'float':
                        $params[] = (float)$rawData[$param];
                        break;
                    case 'string':
                        $params[] = (string)$rawData[$param];
                        break;
                    case 'array':
                        $params[] = (array)$rawData[$param];
                        break;
                    default:
                        $params[] = $rawData[$param];
                        break;
                }
            } elseif (isset($reflectionData['values'][$param])) {
                $params[] = $reflectionData['values'][$param];
            } else {
                $params[] = null;
            }
        }
        return $params;
    }

    /**
     * Is allowed controller/widget/etc
     *
     * @param string $module
     * @param array $reflection
     * @return bool
     */
    public function isAllowed($module, $reflection)
    {
        if (isset($reflection['privilege'])) {
            return Acl::isAllowed($module, $reflection['privilege']);
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
    public function getControllerFile($module, $controller)
    {
        $controllerPath = $this->getPath() . '/modules/' . $module
            . '/controllers/' . $controller . '.php';

        if (!file_exists($controllerPath)) {
            throw new ApplicationException("Controller not found '$module/$controller'", 404);
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
            throw new ApplicationException("Widget not found '$module/$widget'");
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
            throw new ApplicationException("API not found '$module/$method'");
        }

        return $apiPath;
    }
    
    /**
     * Finally method
     * 
     * @return Application
     */
    public function finish()
    {
        $this->log(__METHOD__);
        return $this;
    }
}
