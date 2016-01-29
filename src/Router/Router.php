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
namespace Bluz\Router;

use Bluz\Application\Application;
use Bluz\Common\Options;
use Bluz\Proxy\Cache;
use Bluz\Proxy\Request;

/**
 * Router
 *
 * @package  Bluz\Router
 * @author   Anton Shevchuk
 * @link     https://github.com/bluzphp/framework/wiki/Router
 */
class Router
{
    use Options;

    /**
     * Or should be as properties?
     */
    const DEFAULT_MODULE = 'index';
    const DEFAULT_CONTROLLER = 'index';
    const ERROR_MODULE = 'error';
    const ERROR_CONTROLLER = 'index';

    /**
     * @var string base URL
     */
    protected $baseUrl;

    /**
     * @var string REQUEST_URI minus Base URL
     */
    protected $cleanUri;

    /**
     * @var string default module
     */
    protected $defaultModule = self::DEFAULT_MODULE;

    /**
     * @var string default Controller
     */
    protected $defaultController = self::DEFAULT_CONTROLLER;

    /**
     * @var string error module
     */
    protected $errorModule = self::ERROR_MODULE;

    /**
     * @var string error Controller
     */
    protected $errorController = self::ERROR_CONTROLLER;

    /**
     * @var array instance parameters
     */
    protected $params = array();

    /**
     * @var array instance raw parameters
     */
    protected $rawParams = array();

    /**
     * @var array routers map
     */
    protected $routers = array();

    /**
     * @var array reverse map
     */
    protected $reverse = array();

    /**
     * Constructor of Router
     */
    public function __construct()
    {
        $this->module = $this->getDefaultModule();
        $this->controller = $this->getDefaultController();

        $routers = Cache::get('router:routers');
        $reverse = Cache::get('router:reverse');

        if (!$routers || !$reverse) {
            $routers = array();
            $reverse = array();
            $path = Application::getInstance()->getPath() . '/modules/*/controllers/*.php';
            foreach (new \GlobIterator($path) as $file) {
                /* @var \SplFileInfo $file */
                $module = $file->getPathInfo()->getPathInfo()->getBasename();
                $controller = $file->getBasename('.php');
                $reflection = Application::getInstance()->reflection($file->getRealPath());
                if ($routes = $reflection->getRoute()) {
                    foreach ($routes as $route => $pattern) {
                        if (!isset($reverse[$module])) {
                            $reverse[$module] = array();
                        }

                        $reverse[$module][$controller] = ['route' => $route, 'params' => $reflection->getParams()];

                        $rule = [
                            $route => [
                                'pattern' => $pattern,
                                'module' => $module,
                                'controller' => $controller,
                                'params' => $reflection->getParams()
                            ]
                        ];

                        // static routers should be first
                        if (strpos($route, '$')) {
                            $routers = array_merge($routers, $rule);
                        } else {
                            $routers = array_merge($rule, $routers);
                        }
                    }
                }
            }
            Cache::set('router:routers', $routers);
            Cache::set('router:reverse', $reverse);
        }

        $this->routers = $routers;
        $this->reverse = $reverse;
    }

    /**
     * Get the base URL.
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Set the base URL.
     *
     * @param  string $baseUrl
     * @return void
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = rtrim($baseUrl, '/') . '/';
    }

    /**
     * Get an action parameter
     *
     * @param  string $key
     * @param  mixed  $default Default value to use if key not found
     * @return mixed
     */
    public function getParam($key, $default = null)
    {
        return isset($this->params[$key]) ? $this->params[$key] : $default;
    }

    /**
     * Set an action parameter
     *
     * A $value of null will unset the $key if it exists
     *
     * @param  string $key
     * @param  mixed  $value
     * @return void
     */
    public function setParam($key, $value)
    {
        $key = (string)$key;

        if ((null === $value) && isset($this->params[$key])) {
            unset($this->params[$key]);
        } elseif (null !== $value) {
            $this->params[$key] = $value;
        }
    }

    /**
     * Get parameters
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Get raw params, w/out module and controller
     *
     * @return array
     */
    public function getRawParams()
    {
        return $this->rawParams;
    }

    /**
     * Get default module
     *
     * @return string
     */
    public function getDefaultModule()
    {
        return $this->defaultModule;
    }

    /**
     * Set default module
     *
     * @param  string $defaultModule
     * @return void
     */
    public function setDefaultModule($defaultModule)
    {
        $this->defaultModule = $defaultModule;
    }

    /**
     * Get default controller
     *
     * @return string
     */
    public function getDefaultController()
    {
        return $this->defaultController;
    }

    /**
     * Set default controller
     *
     * @param  string $defaultController
     * @return void
     */
    public function setDefaultController($defaultController)
    {
        $this->defaultController = $defaultController;
    }

    /**
     * Get error module
     *
     * @return string
     */
    public function getErrorModule()
    {
        return $this->errorModule;
    }

    /**
     * Set error module
     *
     * @param  string $errorModule
     * @return void
     */
    public function setErrorModule($errorModule)
    {
        $this->errorModule = $errorModule;
    }

    /**
     * Get error controller
     *
     * @return string
     */
    public function getErrorController()
    {
        return $this->errorController;
    }

    /**
     * Set error controller
     *
     * @param  string $errorController
     * @return void
     */
    public function setErrorController($errorController)
    {
        $this->errorController = $errorController;
    }

    /**
     * Build URL to controller
     *
     * @param  string $module
     * @param  string $controller
     * @param  array  $params
     * @return string
     */
    public function getUrl($module = self::DEFAULT_MODULE, $controller = self::DEFAULT_CONTROLLER, $params = array())
    {
        if (is_null($module)) {
            $module = Request::getModule();
        }

        if (is_null($controller)) {
            $controller = Request::getController();
        }

        if (empty($this->routers)) {
            return $this->urlRoute($module, $controller, $params);
        } else {
            if (isset($this->reverse[$module], $this->reverse[$module][$controller])) {
                return $this->urlCustom($module, $controller, $params);
            }
            return $this->urlRoute($module, $controller, $params);
        }
    }

    /**
     * Build full URL to controller
     *
     * @param  string $module
     * @param  string $controller
     * @param  array  $params
     * @return string
     */
    public function getFullUrl(
        $module = self::DEFAULT_MODULE,
        $controller = self::DEFAULT_CONTROLLER,
        $params = array()
    ) {
        $scheme = Request::getInstance()->getUri()->getScheme() . '://';
        $host = Request::getInstance()->getUri()->getHost();
        $url = $this->getUrl($module, $controller, $params);
        return $scheme . $host . $url;
    }

    /**
     * Build URL by custom route
     *
     * @param  string $module
     * @param  string $controller
     * @param  array  $params
     * @return string
     */
    protected function urlCustom($module, $controller, $params)
    {
        $url = $this->reverse[$module][$controller]['route'];

        $getParams = array();
        foreach ($params as $key => $value) {
            // sub-array as GET params
            if (is_array($value)) {
                $getParams[$key] = $value;
                continue;
            }
            $url = str_replace('{$' . $key . '}', $value, $url, $replaced);
            // if not replaced, setup param as GET
            if (!$replaced) {
                $getParams[$key] = $value;
            }
        }
        // clean optional params
        $url = preg_replace('/\{\$[a-z0-9-_]+\}/i', '', $url);
        // clean regular expression (.*)
        $url = preg_replace('/\(\.\*\)/i', '', $url);
        // replace "//" with "/"
        $url = str_replace('//', '/', $url);

        if (!empty($getParams)) {
            $url .= '?' . http_build_query($getParams);
        }
        return $this->getBaseUrl() . ltrim($url, '/');
    }

    /**
     * Build URL by default route
     *
     * @param  string $module
     * @param  string $controller
     * @param  array  $params
     * @return string
     */
    protected function urlRoute($module, $controller, $params)
    {
        $url = $this->getBaseUrl();

        if (empty($params)) {
            if ($controller == self::DEFAULT_CONTROLLER) {
                if ($module == self::DEFAULT_MODULE) {
                    return $url;
                } else {
                    return $url . $module;
                }
            }
        }

        $url .= $module . '/' . $controller;
        $getParams = array();
        foreach ($params as $key => $value) {
            // sub-array as GET params
            if (is_array($value)) {
                $getParams[$key] = $value;
                continue;
            }
            $url .= '/' . urlencode($key) . '/' . urlencode($value);
        }
        if (!empty($getParams)) {
            $url .= '?' . http_build_query($getParams);
        }
        return $url;
    }

    /**
     * Process routing
     *
     * @return \Bluz\Router\Router
     */
    public function process()
    {
        switch (true) {
            // try process default router
            case $this->processDefault():
                break;
            // try process custom routers
            case $this->processCustom():
                break;
            // try process router
            case $this->processRoute():
                break;
        }

        $this->resetRequest();
        return $this;
    }

    /**
     * Process default router
     *
     * @return bool
     */
    protected function processDefault()
    {
        $uri = $this->getCleanUri();
        return empty($uri);
    }

    /**
     * Process custom router
     *
     * @return bool
     */
    protected function processCustom()
    {
        $uri = '/' . $this->getCleanUri();
        foreach ($this->routers as $router) {
            if (preg_match($router['pattern'], $uri, $matches)) {
                $this->setParam('_module', $router['module']);
                $this->setParam('_controller', $router['controller']);

                foreach ($router['params'] as $param => $type) {
                    if (isset($matches[$param])) {
                        $this->setParam($param, $matches[$param]);
                    }
                }
                return true;
            }
        }
        return false;
    }

    /**
     * Process router by default rules
     *
     * Default routers examples
     *     /
     *     /:module/
     *     /:module/:controller/
     *     /:module/:controller/:key1/:value1/:key2/:value2...
     *
     * @return bool
     */
    protected function processRoute()
    {
        $uri = $this->getCleanUri();
        $uri = trim($uri, '/');
        $raw = explode('/', $uri);

        // rewrite module from request
        if (sizeof($raw)) {
            $this->setParam('_module', array_shift($raw));
        }
        // rewrite module from controller
        if (sizeof($raw)) {
            $this->setParam('_controller', array_shift($raw));
        }
        if ($size = sizeof($raw)) {
            // save raw
            $this->rawParams = $raw;

            // save as index params
            foreach ($raw as $i => $value) {
                $this->setParam($i, $value);
            }

            // remove tail
            if ($size % 2 == 1) {
                array_pop($raw);
                $size = sizeof($raw);
            }
            // or use array_chunk and run another loop?
            for ($i = 0; $i < $size; $i = $i + 2) {
                $this->setParam($raw[$i], $raw[$i + 1]);
            }
        }
        return true;
    }

    /**
     * Reset Request
     *
     * @return void
     */
    protected function resetRequest()
    {
        $request = Request::getInstance();

        // priority:
        //  - default values
        //  - from GET query
        //  - from path
        $request = $request->withQueryParams(
            array_merge(
                [
                    '_module' => $this->getDefaultModule(),
                    '_controller' => $this->getDefaultController()
                ],
                $request->getQueryParams(),
                $this->params
            )
        );
        Request::setInstance($request);
    }

    /**
     * Get the request URI without baseUrl
     *
     * @return string
     */
    public function getCleanUri()
    {
        if ($this->cleanUri === null) {
            $uri = Request::getRequestUri()->getPath();
            if ($this->getBaseUrl() && strpos($uri, $this->getBaseUrl()) === 0) {
                $uri = substr($uri, strlen($this->getBaseUrl()));
            }
            $this->cleanUri = $uri;
        }
        return $this->cleanUri;
    }
}
