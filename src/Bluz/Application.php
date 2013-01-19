<?php
/**
 * Copyright (c) 2012 by Bluz PHP Team
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * @namespace
 */
namespace Bluz;

use Bluz\Acl\Acl;
use Bluz\Auth\Auth;
use Bluz\Cache\Cache;
use Bluz\Config\Config;
use Bluz\Db\Db;
use Bluz\EventManager\Event;
use Bluz\EventManager\EventManager;
use Bluz\Ldap\Ldap;
use Bluz\Mailer\Mailer;
use Bluz\Messages\Messages;
use Bluz\Registry\Registry;
use Bluz\Request;
use Bluz\Router\Router;
use Bluz\Session\Session;
use Bluz\View\Layout;
use Bluz\View\View;
use Bluz\Logger;

/**
 * Application
 *
 * @category Bluz
 * @package  Application
 *
 * @method void denied()
 * @method void reload()
 * @method void redirect(\string $url)
 * @method void redirectTo(\string $module, \string $controller, array $params=array())
 * @method \Bluz\Auth\AbstractEntity user()
 *
 * @author   Anton Shevchuk
 * @created  06.07.11 16:25
 */
class Application
{
    use Singleton;
    use Helper;

    /**
     * @var Acl
     */
    protected $acl;

    /**
     * @var Auth
     */
    protected $auth;

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Db
     */
    protected $db;

    /**
     * @var EventManager
     */
    protected $eventManager;

    /**
     * @var Layout
     */
    protected $layout;

    /**
     * @var Ldap
     */
    protected $ldap;

    /**
     * @var Mailer
     */
    protected $mailer;

    /**
     * @var Messages
     */
    protected $messages;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Request\AbstractRequest
     */
    protected $request;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var string
     */
    protected $environment;

    /**
     * Use layout flag
     * @var boolean
     */
    protected $layoutFlag = true;

    /**
     * JSON response flag
     * @var boolean
     */
    protected $jsonFlag = false;

    /**
     * Widgets closures
     * @var array
     */
    protected $widgets = array();

    /**
     * api closures
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
            // setup configuration for current environment
            $this->getConfig($environment);

            $this->log(__METHOD__);

            // initial default helper path
            $this->addHelperPath(dirname(__FILE__) . '/Application/Helper/');

            // session start inside
            $this->getSession();

            // initial DB configuration
            $this->getDb();
        } catch (Exception $e) {
            throw new Exception("Application can't be loaded: ". $e->getMessage());
        }
        return $this;
    }

    /**
     * log message, working with profiler
     *
     * @param  string $message
     * @return void
     */
    public function log($message)
    {
        $this->getEventManager()->trigger('log', $message);
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
            $this->config->load($environment);
        }
        return $this->config;
    }

    /**
     * load logger
     *
     * @return Logger
     */
    public function getLogger()
    {
        if (!$this->logger) {
            $this->logger = new Logger();
        }
        return $this->logger;
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
        return $this->getConfig()->get($section, $subsection);
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
        if (!$this->auth && $conf = $this->getConfigData('auth')) {
            $this->auth = new Auth();
            $this->auth->setOptions($conf);
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
            $conf = $this->getConfigData('cache');
            if (!isset($conf['enabled']) or !$conf['enabled']) {
                $this->cache = new Nil();
            } else {
                $this->cache = new Cache();
                $this->cache->setOptions($conf);
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
        if (!$this->db && $conf = $this->getConfigData('db')) {
            $this->db = new Db();
            $this->db->setOptions($conf);
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
        if (!$this->layout && $conf = $this->getConfigData('layout')) {
            $this->layout = new Layout();
            $this->layout->setOptions($conf);
        }
        return $this->layout;
    }

    /**
     * resetLayout
     *
     */
    public function resetLayout()
    {
        $this->layout = null;
    }

    /**
     * getLdap
     *
     * @return Ldap
     */
    public function getLdap()
    {
        if (!$this->ldap && $conf = $this->getConfigData('ldap')) {
            $this->ldap = new Ldap();
            $this->ldap->setOptions($conf);
        }
        return $this->ldap;
    }

    /**
     * getMailer
     *
     * @return Mailer
     */
    public function getMailer()
    {
        if (!$this->mailer && $conf = $this->getConfigData('mailer')) {
            $this->mailer = new Mailer();
            $this->mailer->setOptions($conf);
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
        return ($this->messages != null);
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
        }
        return $this->messages;
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
            if ($conf = $this->getConfigData('registry')) {
                $this->registry->setData($conf);
            }
        }
        return $this->registry;
    }

    /**
     * getRequest
     *
     * @return Request\AbstractRequest
     */
    public function getRequest()
    {
        if (!$this->request) {
            $this->request = new Request\HttpRequest();
            $this->request->setOptions($this->getConfigData('request'));

            if ($this->request->isXmlHttpRequest()) {
                $this->useLayout(false);
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
     * getRouter
     *
     * @return Router
     */
    public function getRouter()
    {
        if (!$this->router) {
            $this->router = new Router();
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
            $this->session->start();
            $this->getMessages();
        }
        return $this->session;
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
     * @return mixed
     */
    public function process()
    {
        $this->log(__METHOD__);

        $this->getRequest();

        $this->getRouter()
             ->process();

        if ($this->getRequest()->getParam('_json')) {
            $this->useJson(true);
        }

        try {
            $this->dispatchResult = $this->dispatch(
                $this->request->getModule(),
                $this->request->getController(),
                $this->request->getAllParams()
            );
        } catch (\Exception $e) {
            $this->dispatchResult = $this->dispatch(Router::ERROR_MODULE, Router::ERROR_CONTROLLER, array(
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ));
        }

        return $this->dispatchResult;
    }

    /**
     * dispatch
     *
     * Call dispatch from any \Bluz\Package
     * <code>
     * $this->getApplication()->dispatch($module, $controller, array $params);
     * </code>
     *
     * Attach callback function to event "dispatch"
     * <code>
     * $this->getApplication()->getEventManager()->attach('dispatch', function($event) {
     *     $eventParams = $event->getParams();
     *     $app = $event->getTarget();
     *     \Bluz\Profiler::log('bootstrap:dispatch: '.$eventParams['module'].'/'.$eventParams['controller']);
     * });
     * </code>
     *
     * @param string $module
     * @param string $controller
     * @param array  $params
     * @throws Exception
     * @return View
     */
    public function dispatch($module, $controller, $params = array())
    {
        $this->log(__METHOD__.": ".$module.'/'.$controller);
        $controllerFile = $this->getControllerFile($module, $controller);
        $reflectionData = $this->reflection($controllerFile);

        // system trigger "dispatch"
        $this->getEventManager()->trigger('dispatch', $this, array(
            'module' => $module,
            'controller' => $controller,
            'params' => $params,
            'reflection' => $reflectionData
        ));

        // check acl
        if (!$this->isAllowedController($module, $controller, $params)) {
            $this->denied();
        }

        // cache initialization
        if (isset($reflectionData['cache'])) {
            $cacheKey = $module .'/'. $controller .'/'. http_build_query($params);
            if ($cachedView = $this->getCache()->get($cacheKey)) {
                return $cachedView;
            }
        }

        // process params
        $params = $this->params($reflectionData, $params);

        // $view for use in closure
        $view = new View();
        // setup default path
        $view->setPath(PATH_APPLICATION .'/modules/'. $module .'/views');
        // setup default template
        $view->setTemplate($controller .'.phtml');

        $bootstrapPath = PATH_APPLICATION .'/modules/' . $module .'/bootstrap.php';

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
            throw new Exception("Controller is not callable '$module/$controller'");
        }

        $result = call_user_func_array($controllerClosure, $params);

        // return false is equal to disable view and layout
        if ($result === false) {
            $this->useLayout(false);
            return false;
        }

        // return closure is replace logic of controller
        if (is_callable($result)) {
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
            $this->getCache()->set($cacheKey, $view, intval($reflectionData['cache'])*60);
            $this->getCache()->addTag($cacheKey, 'view');
            $this->getCache()->addTag($cacheKey, 'view:'.$module);
            $this->getCache()->addTag($cacheKey, 'view:'.$module.':'.$controller);
        }

        return $view;
    }

    /**
     * render
     *
     * @return Application
     */
    public function render()
    {
        $this->log(__METHOD__);

        $result = $this->dispatchResult;

        // browser render
        if ($this->jsonFlag) {
            // override response code so javascript can process it
            header('Content-type: application/json', true, 200);

            // get data from layout
            $data = $this->getLayout()->toArray();

            // merge it with view data
            if ($result instanceof View) {
                $data = array_merge($data, $result->toArray());
            }

            // enable Bluz AJAX handler
            if (!isset($data['_handler'])) {
                $data['_handler'] = true;
            }

            // inject messages if exists
            if (!isset($data['_messages']) && $this->hasMessages()) {
                $data['_messages'] = $this->getMessages()->popAll();
            }

            // output
            echo json_encode($data);
        } elseif (!$this->layoutFlag) {
            echo ($result instanceof \Closure) ? $result() : $result;
        } else {
            $this->getLayout()->setContent($result);
            echo $this->getLayout();
        }
        return $this;
    }

    /**
     * render for CLI
     *
     * @return Application
     */
    public function output()
    {
        $this->log(__METHOD__);

        $result = $this->dispatchResult;

        // get data from layout
        $data = $this->getLayout()->toArray();

        // merge it with view data
        if ($result instanceof View) {
            $data = array_merge($data, $result->toArray());
        }

        // inject messages if exists
        if ($this->hasMessages()) {
            while ($msg = $this->getMessages()->pop(Messages::TYPE_ERROR)) {
                echo "\033[41m\033[1;37mError    \033[m\033m: ";
                echo $msg->text ."\n";
            }
            while ($msg = $this->getMessages()->pop(Messages::TYPE_NOTICE)) {
                echo "\033[44m\033[1;37mInfo     \033[m\033m: ";
                echo $msg->text ."\n";
            }
            while ($msg = $this->getMessages()->pop(Messages::TYPE_SUCCESS)) {
                echo "\033[42m\033[1;37mSuccess  \033[m\033m: ";
                echo $msg->text ."\n";
            }
            echo "\n";
        }
        foreach ($data as $key => $value) {
            echo "\033[1;33m$key\033[m:\n";
            var_dump($value);
            echo "\n";
        }

        return $this;
    }

    /**
     * widget
     *
     * Call widget from any \Bluz\Package
     * <code>
     * $this->getApplication()->widget($module, $widget, array $params);
     * </code>
     *
     * Attach callback function to event "widget"
     * <code>
     * $this->getApplication()->getEventManager()->attach('widget', function($event) {
     *     $eventParams = $event->getParams();
     *     $app = $event->getTarget();
     *     \Bluz\Profiler::log('bootstrap:dispatch: '.$eventParams['module'].'/'.$eventParams['widget']);
     * });
     * </code>
     *
     * @param string $module
     * @param string $widget
     * @param array  $params
     * @throws Exception
     * @return \Closure
     */
    public function widget($module, $widget, $params = array())
    {
        $this->log(__METHOD__.": ".$module.'/'.$widget);
        $widgetFile = $this->getWidgetFile($module, $widget);
        $reflectionData = $this->reflection($widgetFile);

        $this->getEventManager()->trigger('widget', $this, array(
            'module' => $module,
            'widget' => $widget,
            'params' => $params,
            'reflection' => $reflectionData
        ));

        /**
         * Cachable widgets
         * @var \Closure $widgetClosure
         */
        if (isset($this->widgets[$module])
            && isset($this->widgets[$module][$widget])) {
            $widgetClosure = $this->widgets[$module][$widget];
        } else {
            $widgetClosure = require $this->getWidgetFile($module, $widget);

            if (!isset($this->widgets[$module])) {
                $this->widgets[$module] = array();
            }
            $this->widgets[$module][$widget] = $widgetClosure;
        }

        if (!is_callable($widgetClosure)) {
            throw new Exception("Widget is not callable '$module/$widget'");
        }

        return $widgetClosure;
    }

    /**
     * api
     *
     * Call API from any \Bluz\Package
     * <code>
     * $this->getApplication()->api($module, $widget, array $params);
     * </code>
     *
     * Attach callback function to event "api"
     * <code>
     * $this->getApplication()->getEventManager()->attach('api', function($event) {
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
        $this->log(__METHOD__.": ".$module.'/'.$method);

        $this->getEventManager()->trigger('api', $this, array(
            'module' => $module,
            'method' => $method
        ));

        /**
         * Cachable APIs
         * @var \Closure $widgetClosure
         */
        if (isset($this->api[$module])
            && isset($this->api[$module][$method])) {
            $apiClosure = $this->api[$module][$method];
        } else {
            $apiClosure = require $this->getApiFile($module, $method);

            if (!isset($this->api[$module])) {
                $this->api[$module] = array();
            }
            $this->api[$module][$method] = $apiClosure;
        }

        if (!is_callable($apiClosure)) {
            throw new Exception("API is not callable '$module/$method'");
        }

        return $apiClosure;
    }

    /**
     * reflection for anonymous function
     *
     * @param string  $file
     * @throws Exception
     * @return array
     */
    public function reflection($file)
    {
        // cache for reflection data
        if (!$data = $this->getCache()->get('reflection:'.$file)) {

            // TODO: workaround for get reflection of closure function
            $bootstrap = $view = $module = $controller = null;
            $closure = include $file;

            if (!is_callable($closure)) {
                throw new Exception("There is no closure in file $file");
            }

            $reflection = new \ReflectionFunction($closure);

            // check and normalize params by doc comment
            $docComment = $reflection->getDocComment();
            preg_match_all('/\s*\*\s*\@param\s+(bool|boolean|int|integer|float|string|array)\s+\$([a-z0-9_]+)/i', $docComment, $matches);

            // init data
            $data = array();

            // rebuild array
            $data['types'] = array();
            foreach ($matches[1] as $i => $type) {
                $data['types'][$matches[2][$i]] = $type;
            }

            // get params and convert it to simple array
            $params = $reflection->getParameters();
            foreach ($params as $key => $param) {
                $params[$key] = $param->getName();
            }
            $data['params'] = $params;

            // check cache settings
            if (preg_match('/\s*\*\s*\@cache\s+([0-9\.]+).*/i', $docComment, $matches)) {
                $data['cache'] = (int) $matches[1];
            }
            // check routers
            if (preg_match('/\s*\*\s*\@route\s+(.*)\s*/i', $docComment, $matches)) {
                $data['route'] = $matches[1];
            }
            // check acl settings
            if (preg_match('/\s*\*\s*\@privilege\s+(\w+).*/i', $docComment, $matches)) {
                $data['privilege'] = $matches[1];
            }
            $this->getCache()->set('reflection:'.$file, $data);
            $this->getCache()->addTag('reflection:'.$file, 'reflection');
        }
        return $data;
    }

    /**
     * process params
     *
     * @param array $reflectionData
     * @param array $rawData
     * @return array
     */
    private function params($reflectionData, $rawData)
    {
        // need use new array for order params as described in controller
        $params = array();
        foreach ($reflectionData['params'] as $param) {
            if (isset($rawData[$param])
                && isset($reflectionData['types'][$param])
                && $type = $reflectionData['types'][$param]) {
                    switch ($type) {
                        case 'bool':
                        case 'boolean':
                            $params[] = (bool) $rawData[$param];
                            break;
                        case 'int':
                        case 'integer':
                            $params[] = (int) $rawData[$param];
                            break;
                        case 'float':
                            $params[] = (float) $rawData[$param];
                            break;
                        case 'string':
                            $params[] = (string) $rawData[$param];
                            break;
                        case 'array':
                            $params[] = (array) $rawData[$param];
                            break;
                    }
            } elseif (isset($rawData[$param])) {
                $params[] = $rawData[$param];
            } else {
                $params[] = null;
            }
        }
        return $params;
    }

    /**
     * Is allowed controller
     *
     * @param string $module
     * @param string $controller
     * @param array  $params
     * @return boolean
     */
    public function isAllowedController($module, $controller, array $params = array())
    {
        $controllerFile = $this->getControllerFile($module, $controller);

        $data = $this->reflection($controllerFile);

        if (isset($data['privilege']) &&
            !$this->getAcl()->isAllowed($module, $data['privilege'])) {
            // privilege is described and deny
            return false;
        }

        return true;
    }

    /**
     * Is allowed widget
     *
     * @param string $module
     * @param string $widget
     * @param array  $params
     * @return boolean
     */
    public function isAllowedWidget($module, $widget, array $params = array())
    {
        $widgetFile = $this->getWidgetFile($module, $widget);

        $data = $this->reflection($widgetFile);

        if (isset($data['privilege']) &&
            !$this->getAcl()->isAllowed($module, $data['privilege'])) {
            // privilege is described and deny
            return false;
        }

        return true;
    }

    /**
     * Get controller file
     *
     * @param  string $module
     * @param  string $controller
     * @return \Closure
     * @throws Exception
     */
    protected function getControllerFile($module, $controller)
    {
        $controllerPath = PATH_APPLICATION . '/modules/' . $module
                        .'/controllers/' . $controller .'.php';

        if (!file_exists($controllerPath)) {
            throw new Exception("Controller not found '$module/$controller'", 404);
        }

        return $controllerPath;
    }

    /**
     * Get widget file
     *
     * @param  string $module
     * @param  string $widget
     * @return \Closure
     * @throws Exception
     */
    protected function getWidgetFile($module, $widget)
    {
        $widgetPath = PATH_APPLICATION . '/modules/' . $module
                        .'/widgets/' . $widget .'.php';

        if (!file_exists($widgetPath)) {
            throw new Exception("Widget not found '$module/$widget'");
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
        $apiPath = PATH_APPLICATION . '/modules/' . $module
                        .'/api/' . $method .'.php';

        if (!file_exists($apiPath)) {
            throw new Exception("API not found '$module/$method'");
        }

        return $apiPath;
    }
}
