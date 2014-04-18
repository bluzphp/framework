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

use Bluz\Acl\Acl;
use Bluz\Acl\AclException;
use Bluz\Application\Exception\ApplicationException;
use Bluz\Application\Exception\RedirectException;
use Bluz\Application\Exception\ReloadException;
use Bluz\Auth\Auth;
use Bluz\Cache\Cache;
use Bluz\Common\Exception;
use Bluz\Common\Helper;
use Bluz\Common\Nil;
use Bluz\Common\Singleton;
use Bluz\Config\Config;
use Bluz\Config\ConfigException;
use Bluz\Db\Db;
use Bluz\EventManager\EventManager;
use Bluz\Http;
use Bluz\Logger\Logger;
use Bluz\Mailer\Mailer;
use Bluz\Messages\Messages;
use Bluz\Registry\Registry;
use Bluz\Request;
use Bluz\Response;
use Bluz\Router\Router;
use Bluz\Session\Session;
use Bluz\Translator\Translator;
use Bluz\View\Layout;
use Bluz\View\View;

/**
 * Application
 *
 * @package  Bluz\Application
 *
 * @method void denied()
 * @method void redirect(\string $url)
 * @method void redirectTo(\string $module, \string $controller, array $params = array())
 * @method void reload()
 * @method \Bluz\Auth\AbstractRowEntity user()
 *
 * @author   Anton Shevchuk
 * @created  06.07.11 16:25
 */
abstract class Application
{
    use Singleton;
    use Helper;

    /**
     * @var Acl instance
     */
    protected $acl;

    /**
     * @var Auth instance
     */
    protected $auth;

    /**
     * @var Cache instance
     */
    protected $cache;

    /**
     * @var Config instance
     */
    protected $config;

    /**
     * @var Db instance
     */
    protected $db;

    /**
     * @var EventManager instance
     */
    protected $eventManager;

    /**
     * @var Layout instance
     */
    protected $layout;

    /**
     * @var Logger instance
     */
    protected $logger;

    /**
     * @var Mailer instance
     */
    protected $mailer;

    /**
     * @var Messages instance
     */
    protected $messages;

    /**
     * Application path
     *
     * @var string
     */
    protected $path;

    /**
     * @var Registry instance
     */
    protected $registry;

    /**
     * @var Request\AbstractRequest instance
     */
    protected $request;

    /**
     * @var Response\AbstractResponse instance
     */
    protected $response;

    /**
     * @var Router instance
     */
    protected $router;

    /**
     * @var Session instance
     */
    protected $session;

    /**
     * @var Translator instance
     */
    protected $translator;

    /**
     * Environment name
     * @var string
     */
    protected $environment;

    /**
     * Debug application flag
     * @var boolean
     */
    protected $debugFlag = false;

    /**
     * Layout flag
     * @var boolean
     */
    protected $layoutFlag = true;

    /**
     * JSON response flag
     * @var boolean
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
     * Temporary variable for save dispatch result
     * @var null
     */
    protected $dispatchResult = null;

    /**
     * init
     *
     * @param string $environment Array format only!
     * @throws Exception
     * @return Application
     */
    public function init($environment = 'production')
    {
        $this->environment = $environment;

        try {
            // initial default helper path
            $this->addHelperPath(dirname(__FILE__) . '/Helper/');

            // setup configuration for current environment
            $this->getConfig($environment);

            if ($debug = $this->getConfigData('debug')) {
                $this->debugFlag = $debug;
            }

            // first log message
            $this->log('app:init');

            // initial session, start inside class
            $this->getSession();

            // initial Translator
            $this->getTranslator();

            // initial DB configuration
            $this->getDb();
        } catch (Exception $e) {
            throw new ApplicationException("Application can't be loaded: " . $e->getMessage());
        }
        return $this;
    }

    /**
     * log message, working with logger
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function log($message, array $context = [])
    {
        $this->getLogger()->info($message, $context);
    }

    /**
     * load config file
     *
     * @param string|null $environment
     * @return Config
     */
    public function getConfig($environment = null)
    {
        if (!$this->config) {
            $this->config = new Config();
            $this->config->setPath($this->getPath() . '/configs');
            $this->config->load($environment);
        }
        return $this->config;
    }

    /**
     * config
     *
     * @param string|null $section of config
     * @param string|null $subsection of config
     * @return array
     */
    public function getConfigData($section = null, $subsection = null)
    {
        return $this->getConfig()->getData($section, $subsection);
    }

    /**
     * getAcl
     *
     * @return Acl
     */
    public function getAcl()
    {
        if (!$this->acl) {
            $this->acl = new Acl();
        }
        return $this->acl;
    }

    /**
     * getAuth
     *
     * @return Auth
     */
    public function getAuth()
    {
        if (!$this->auth && $config = $this->getConfigData('auth')) {
            $this->auth = new Auth();
            $this->auth->setOptions($config);
        }
        return $this->auth;
    }

    /**
     * if enabled return configured Cache or Nil otherwise
     *
     * @return Cache|Nil
     */
    public function getCache()
    {
        if (!$this->cache) {
            $config = $this->getConfigData('cache');
            if (!isset($config['enabled']) or !$config['enabled']) {
                $this->cache = new Nil();
            } else {
                $this->cache = new Cache();
                $this->cache->setOptions($config);
            }
        }
        return $this->cache;
    }

    /**
     * getDb
     *
     * @return Db
     */
    public function getDb()
    {
        if (!$this->db) {
            $this->db = new Db();
            $this->db->setOptions($this->getConfigData('db'));
        }
        return $this->db;
    }

    /**
     * getEventManager
     *
     * @return EventManager
     */
    public function getEventManager()
    {
        if (!$this->eventManager) {
            $this->eventManager = new EventManager();
        }
        return $this->eventManager;
    }

    /**
     * getLayout
     *
     * @return Layout
     */
    public function getLayout()
    {
        if (!$this->layout) {
            $this->layout = new Layout();
            $this->layout->setOptions($this->getConfigData('layout'));
        }
        return $this->layout;
    }

    /**
     * Reset Layout, required for tests only
     *
     * @return Application
     */
    public function resetLayout()
    {
        $this->layout = null;
        return $this;
    }

    /**
     * load logger
     *
     * @return Logger
     */
    public function getLogger()
    {
        if (!$this->logger) {
            $config = $this->getConfigData('logger');
            if (!isset($config['enabled']) or !$config['enabled']) {
                $this->logger = new Nil();
            } else {
                $this->logger = new Logger();
            }
        }
        return $this->logger;
    }

    /**
     * getMailer
     *
     * @throws ConfigException
     * @return Mailer
     */
    public function getMailer()
    {
        if (!$this->mailer) {
            if ($config = $this->getConfigData('mailer')) {
                $this->mailer = new Mailer();
                $this->mailer->setOptions($config);
            } else {
                throw new ConfigException(
                    "Missed `mailer` options in configuration file. <br/>\n" .
                    "Read more: <a href='https://github.com/bluzphp/framework/wiki/Mailer'>".
                    "https://github.com/bluzphp/framework/wiki/Mailer"."</a>"
                );
            }
        }
        return $this->mailer;
    }

    /**
     * hasMessages
     *
     * @return boolean
     */
    public function hasMessages()
    {
        if ($this->messages != null) {
            return ($this->messages->count() > 0);
        } else {
            return false;
        }
    }

    /**
     * getMessages
     *
     * @return Messages
     */
    public function getMessages()
    {
        if (!$this->messages) {
            $this->messages = new Messages();
            $this->messages->setOptions($this->getConfigData('messages'));
        }
        return $this->messages;
    }

    /**
     * getPath
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
     * getRegistry
     *
     * @return Registry
     */
    public function getRegistry()
    {
        if (!$this->registry) {
            $this->registry = new Registry();
            if ($data = $this->getConfigData('registry')) {
                $this->registry->setData($data);
            }
        }
        return $this->registry;
    }

    /**
     * getRequest
     *
     * @return Http\Request
     */
    public function getRequest()
    {
        if (!$this->request) {
            $this->request = new Http\Request();
            $this->request->setOptions($this->getConfigData('request'));

            if ($this->request->isXmlHttpRequest()) {
                $this->useLayout(false);

                // check header "accept" for catch AJAX JSON requests, and switch to JSON response
                $accept = $this->getRequest()->getHeader('accept');

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
     * setRequest
     *
     * @param Request\AbstractRequest $request
     * @return Application
     */
    public function setRequest($request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * getResponse
     *
     * @return Response\AbstractResponse
     */
    public function getResponse()
    {
        if (!$this->response) {
            $this->response = new Http\Response();
            $this->response->setOptions($this->getConfigData('response'));
        }
        return $this->response;
    }

    /**
     * setResponse
     *
     * @param Response\AbstractResponse $response
     * @return Application
     */
    public function setResponse($response)
    {
        $this->response = $response;
        return $this;
    }

    /**
     * getRouter
     *
     * @return Router
     */
    public function getRouter()
    {
        if (!$this->router) {
            $this->router = new Router();
            $this->router->process();
        }
        return $this->router;
    }

    /**
     * getSession
     *
     * @return Session
     */
    public function getSession()
    {
        if (!$this->session) {
            $this->session = new Session();
            $this->session->setOptions($this->getConfigData('session'));

            $this->getMessages();
        }
        return $this->session;
    }

    /**
     * getTranslator
     *
     * @return Translator
     */
    public function getTranslator()
    {
        if (!$this->translator) {
            $this->translator = new Translator();
            $this->translator->setOptions($this->getConfigData('translator'));
        }
        return $this->translator;
    }

    /**
     * return new instance of view
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
     * isDebug
     *
     * @return boolean
     */
    public function isDebug()
    {
        return $this->debugFlag;
    }

    /**
     * isJson
     *
     * @return boolean
     */
    public function isJson()
    {
        return $this->jsonFlag;
    }

    /**
     * hasLayout
     *
     * @return boolean|string
     */
    public function hasLayout()
    {
        return $this->layoutFlag;
    }

    /**
     * useLayout
     *
     * @param boolean|string $flag
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
     * useJson
     *
     * @param boolean $flag
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
     * process
     *
     * - Why you don't use "X-" prefix?
     * - Because it deprecated
     * @link http://tools.ietf.org/html/rfc6648
     *
     * @return mixed
     */
    public function process()
    {
        $this->log('app:process');

        // init request
        $request = $this->getRequest();

        // init router
        $this->getRouter();

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
                $response->setCode(204);
                $response->setHeader('Bluz-Redirect', $e->getMessage());
            } else {
                $response->setCode($e->getCode());
                $response->setHeader('Location', $e->getMessage());
            }
        } catch (ReloadException $e) {
            $response->setException($e);

            if ($request->isXmlHttpRequest()) {
                $response->setCode(204);
                $response->setHeader('Bluz-Reload', 'true');
            } else {
                $response->setCode($e->getCode());
                $response->setHeader('Refresh', '15; url=' . $request->getRequestUri());
            }
        } catch (\Exception $e) {

            $dispatchResult = $this->dispatch(
                Router::ERROR_MODULE,
                Router::ERROR_CONTROLLER,
                array(
                    'code' => $e->getCode(),
                    'message' => $e->getMessage()
                )
            );

            if ($this->hasLayout()) {
                $this->getLayout()->setContent($dispatchResult);
                $dispatchResult = $this->getLayout();
            }

            $this->getResponse()
                ->setException($e)
                ->setCode($e->getCode())
                ->setBody($dispatchResult);
        }

        return $this->getResponse();
    }

    /**
     * Pre dispatch mount point
     *
     * @param string $module
     * @param string $controller
     * @param array $params
     *
     * @throws Exception
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
     * @throws Exception
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
            if ($cachedView = $this->getCache()->get($cacheKey)) {
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
            $view->setData($result);
        }

        if (isset($reflectionData['cache'])) {
            $this->getCache()->set($cacheKey, $view, intval($reflectionData['cache']) * 60);
            $this->getCache()->addTag($cacheKey, 'view');
            $this->getCache()->addTag($cacheKey, 'view:' . $module);
            $this->getCache()->addTag($cacheKey, 'view:' . $module . ':' . $controller);
        }

        return $view;
    }

    /**
     * postDispatch
     *
     * @param string $module
     * @param string $controller
     * @param array $params
     * @throws Exception
     *
     * @return void
     */
    protected function postDispatch($module, $controller, $params = array())
    {
        $this->log("app:dispatch:post: " . $module . '/' . $controller);
    }

    /**
     * dispatch
     *
     * Call dispatch from any \Bluz\Package
     * <code>
     * app()->dispatch($module, $controller, array $params);
     * </code>
     *
     * Attach callback function to event "dispatch"
     * <code>
     * app()->getEventManager()->attach('dispatch', function($event) {
     *     $eventParams = $event->getParams();
     *     $app = $event->getTarget();
     *     \Bluz\Profiler::log('bootstrap:dispatch: '.$eventParams['module'].'/'.$eventParams['controller']);
     * });
     * </code>
     *
     * @param string $module
     * @param string $controller
     * @param array $params
     * @throws Exception
     * @return View
     */
    public function dispatch($module, $controller, $params = array())
    {
        $this->log("app:dispatch: " . $module . '/' . $controller);

        // system trigger "dispatch"
        $this->getEventManager()->trigger(
            'dispatch',
            $this,
            array(
                'module' => $module,
                'controller' => $controller,
                'params' => $params
            )
        );

        $this->preDispatch($module, $controller, $params);
        $result = $this->doDispatch($module, $controller, $params);
        $this->postDispatch($module, $controller, $params);

        return $result;
    }

    /**
     * render
     *
     * @return void
     */
    public function render()
    {
        $this->log('app:render');

        $this->getResponse()->send();
    }

    /**
     * widget
     *
     * Call widget from any \Bluz\Package
     * <code>
     * app()->widget($module, $widget, array $params);
     * </code>
     *
     * Attach callback function to event "widget"
     * <code>
     * app()->getEventManager()->attach('widget', function($event) {
     *     $eventParams = $event->getParams();
     *     $app = $event->getTarget();
     *     \Bluz\Profiler::log('bootstrap:dispatch: '.$eventParams['module'].'/'.$eventParams['widget']);
     * });
     * </code>
     *
     * @param string $module
     * @param string $widget
     * @param array $params
     * @throws Exception
     * @return \Closure
     */
    public function widget($module, $widget, $params = array())
    {
        $this->log(__METHOD__ . ": " . $module . '/' . $widget);
        $widgetFile = $this->getWidgetFile($module, $widget);
        $reflectionData = $this->reflection($widgetFile);


        $this->getEventManager()->trigger(
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
            throw new AclException("Not enough permissions for call widget '$module/$widget'");
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
     * api
     *
     * Call API from any \Bluz\Package
     * <code>
     * app()->api($module, $widget, array $params);
     * </code>
     *
     * Attach callback function to event "api"
     * <code>
     * app()->getEventManager()->attach('api', function($event) {
     *     $eventParams = $event->getParams();
     *     $app = $event->getTarget();
     *     \Bluz\Profiler::log('bootstrap:dispatch: '.$eventParams['module'].'/'.$eventParams['widget']);
     * });
     * </code>
     *
     * @param string $module
     * @param string $method
     * @throws Exception
     * @return \Closure
     */
    public function api($module, $method)
    {
        $this->log(__METHOD__ . ": " . $module . '/' . $method);

        $this->getEventManager()->trigger(
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
     * reflection for anonymous function
     *
     * @param string $file
     * @throws Exception
     * @return array
     */
    public function reflection($file)
    {
        // cache for reflection data
        if (!$data = $this->getCache()->get('reflection:' . $file)) {

            // TODO: workaround for get reflection of closure function
            $bootstrap = $view = $module = $controller = null;
            $closure = include $file;

            if (!is_callable($closure)) {
                throw new Exception("There is no closure in file $file");
            }

            // init data
            $data = array(
                'params' => [],
                'values' => [],
            );

            switch (get_class($closure)) {
                case 'Closure':
                    $reflection = new \ReflectionFunction($closure);
                    break;
                default:
                    $reflection = new \ReflectionObject($closure);
                    break;
            }

            // check and normalize params by doc comment
            $docComment = $reflection->getDocComment();

            // get all options by one regular expression
            if (preg_match_all('/\s*\*\s*\@([a-z0-9-_]+)\s+(.*).*/i', $docComment, $matches)) {
                foreach ($matches[1] as $i => $key) {
                    $data[$key][] = trim($matches[2][$i]);
                }
            }
            if (method_exists($reflection, 'getParameters')) {
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

            $this->getCache()->set('reflection:' . $file, $data);
            $this->getCache()->addTag('reflection:' . $file, 'reflection');
        }
        return $data;
    }

    /**
     * process params:
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
     * @return boolean
     */
    public function isAllowed($module, $reflection)
    {
        if (isset($reflection['privilege'])) {
            return $this->getAcl()->isAllowed($module, $reflection['privilege']);
        }
        return true;
    }

    /**
     * Get controller file
     *
     * @param  string $module
     * @param  string $controller
     * @return string
     * @throws Exception
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
     * @throws Exception
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
     * @return \Closure
     * @throws Exception
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
