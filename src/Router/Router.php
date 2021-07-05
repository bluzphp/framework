<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Router;

use Bluz\Application\Application;
use Bluz\Common\Exception\CommonException;
use Bluz\Common\Exception\ComponentException;
use Bluz\Common\Options;
use Bluz\Controller\Controller;
use Bluz\Controller\ControllerException;
use Bluz\Proxy\Cache;
use Bluz\Proxy\Request;
use ReflectionException;

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
    private const DEFAULT_MODULE = 'index';
    private const DEFAULT_CONTROLLER = 'index';
    private const ERROR_MODULE = 'error';
    private const ERROR_CONTROLLER = 'index';

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
    protected $params = [];

    /**
     * @var array instance raw parameters
     */
    protected $rawParams = [];

    /**
     * @var array[] routers map
     */
    protected $routers = [];

    /**
     * @var array[] reverse map
     */
    protected $reverse = [];

    /**
     * Constructor of Router
     */
    public function __construct()
    {
        $routers = Cache::get('router.routers');
        $reverse = Cache::get('router.reverse');

        if (!$routers || !$reverse) {
            [$routers, $reverse] = $this->prepareRouterData();
            Cache::set('router.routers', $routers, Cache::TTL_NO_EXPIRY, ['system']);
            Cache::set('router.reverse', $reverse, Cache::TTL_NO_EXPIRY, ['system']);
        }

        $this->routers = $routers;
        $this->reverse = $reverse;
    }

    /**
     * Initial routers data from controllers
     *
     * @return array[]
     * @throws CommonException
     * @throws ComponentException
     * @throws ControllerException
     * @throws ReflectionException
     */
    private function prepareRouterData(): array
    {
        $routers = [];
        $reverse = [];
        $path = Application::getInstance()->getPath() . '/modules/*/controllers/*.php';
        foreach (new \GlobIterator($path) as $file) {
            /* @var \SplFileInfo $file */
            $module = $file->getPathInfo()->getPathInfo()->getBasename();
            $controller = $file->getBasename('.php');
            $controllerInstance = new Controller($module, $controller);
            $meta = $controllerInstance->getMeta();
            if ($routes = $meta->getRoute()) {
                foreach ($routes as $route => $pattern) {
                    if (!isset($reverse[$module])) {
                        $reverse[$module] = [];
                    }

                    $reverse[$module][$controller] = ['route' => $route, 'params' => $meta->getParams()];

                    $rule = [
                        $route => [
                            'pattern' => $pattern,
                            'module' => $module,
                            'controller' => $controller,
                            'params' => $meta->getParams()
                        ]
                    ];

                    // static routers should be first, than routes with variables `$...`
                    // all routes begin with slash `/`
                    if (strpos($route, '$')) {
                        $routers[] = $rule;
                    } else {
                        array_unshift($routers, $rule);
                    }
                }
            }
        }
        $routers = array_merge(...$routers);
        return [$routers, $reverse];
    }

    /**
     * Get the base URL.
     *
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Set the base URL.
     *
     * @param string $baseUrl
     *
     * @return void
     */
    public function setBaseUrl(string $baseUrl): void
    {
        $this->baseUrl = str_trim_end($baseUrl, '/');
    }

    /**
     * Get an action parameter
     *
     * @param string $key
     * @param mixed  $default Default value to use if key not found
     *
     * @return mixed
     */
    public function getParam(string $key, $default = null)
    {
        return $this->params[$key] ?? $default;
    }

    /**
     * Set an action parameter
     *
     * A $value of null will unset the $key if it exists
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return void
     */
    public function setParam(string $key, $value): void
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
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Get raw params, w/out module and controller
     *
     * @return array
     */
    public function getRawParams(): array
    {
        return $this->rawParams;
    }

    /**
     * Get default module
     *
     * @return string
     */
    public function getDefaultModule(): string
    {
        return $this->defaultModule;
    }

    /**
     * Set default module
     *
     * @param string $defaultModule
     *
     * @return void
     */
    public function setDefaultModule(string $defaultModule): void
    {
        $this->defaultModule = $defaultModule;
    }

    /**
     * Get default controller
     *
     * @return string
     */
    public function getDefaultController(): string
    {
        return $this->defaultController;
    }

    /**
     * Set default controller
     *
     * @param string $defaultController
     *
     * @return void
     */
    public function setDefaultController(string $defaultController): void
    {
        $this->defaultController = $defaultController;
    }

    /**
     * Get error module
     *
     * @return string
     */
    public function getErrorModule(): string
    {
        return $this->errorModule;
    }

    /**
     * Set error module
     *
     * @param string $errorModule
     *
     * @return void
     */
    public function setErrorModule(string $errorModule): void
    {
        $this->errorModule = $errorModule;
    }

    /**
     * Get error controller
     *
     * @return string
     */
    public function getErrorController(): string
    {
        return $this->errorController;
    }

    /**
     * Set error controller
     *
     * @param string $errorController
     *
     * @return void
     */
    public function setErrorController(string $errorController): void
    {
        $this->errorController = $errorController;
    }

    /**
     * Get the request URI without baseUrl
     *
     * @return string
     */
    public function getCleanUri(): string
    {
        if ($this->cleanUri === null) {
            $uri = Request::getUri()->getPath();
            if ($this->getBaseUrl() && strpos($uri, $this->getBaseUrl()) === 0) {
                $uri = substr($uri, strlen($this->getBaseUrl()));
            }
            $this->cleanUri = $uri;
        }
        return $this->cleanUri;
    }

    /**
     * Build URL to controller
     *
     * @param string|null $module
     * @param string|null $controller
     * @param array $params
     *
     * @return string
     */
    public function getUrl(
        ?string $module = self::DEFAULT_MODULE,
        ?string $controller = self::DEFAULT_CONTROLLER,
        array $params = []
    ): string {
        $module = $module ?? Request::getModule();
        $controller = $controller ?? Request::getController();

        if (isset($this->reverse[$module][$controller])) {
            return $this->urlCustom($module, $controller, $params);
        }

        return $this->urlRoute($module, $controller, $params);
    }

    /**
     * Build full URL to controller
     *
     * @param string $module
     * @param string $controller
     * @param array  $params
     *
     * @return string
     */
    public function getFullUrl(
        string $module = self::DEFAULT_MODULE,
        string $controller = self::DEFAULT_CONTROLLER,
        array $params = []
    ): string {
        $scheme = Request::getUri()->getScheme() . '://';
        $host = Request::getUri()->getHost();
        $port = Request::getUri()->getPort();
        if ($port && !in_array($port, [80, 443], true)) {
            $host .= ':' . $port;
        }
        $url = $this->getUrl($module, $controller, $params);
        return $scheme . $host . $url;
    }

    /**
     * Build URL by custom route
     *
     * @param string $module
     * @param string $controller
     * @param array $params
     *
     * @return string
     */
    protected function urlCustom(string $module, string $controller, array $params): string
    {
        $url = $this->reverse[$module][$controller]['route'];

        $getParams = [];
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
        $url = preg_replace('/\(\.\*\)/', '', $url);
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
     * @param string $module
     * @param string $controller
     * @param array $params
     *
     * @return string
     */
    protected function urlRoute(string $module, string $controller, array $params): string
    {
        $url = $this->getBaseUrl();

        if (empty($params) && $controller === self::DEFAULT_CONTROLLER) {
            if ($module === self::DEFAULT_MODULE) {
                return $url;
            }
            return $url . $module;
        }

        $url .= $module . '/' . $controller;
        $getParams = [];
        foreach ($params as $key => $value) {
            // sub-array as GET params
            if (is_array($value)) {
                $getParams[$key] = $value;
                continue;
            }
            $url .= '/' . urlencode((string)$key) . '/' . urlencode((string)$value);
        }
        if (!empty($getParams)) {
            $url .= '?' . http_build_query($getParams);
        }
        return $url;
    }

    /**
     * Process routing
     *
     * @return void
     */
    public function process(): void
    {
        $this->processDefault() || // try to process default router (homepage)
        $this->processCustom() ||  //  or custom routers
        $this->processRoute();     //  or default router schema

        $this->resetRequest();
    }

    /**
     * Process default router
     *
     * @return bool
     */
    protected function processDefault(): bool
    {
        $uri = $this->getCleanUri();
        return empty($uri);
    }

    /**
     * Process custom router
     *
     * @return bool
     */
    protected function processCustom(): bool
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
    protected function processRoute(): bool
    {
        $uri = $this->getCleanUri();
        $uri = trim($uri, '/');
        $raw = explode('/', $uri);

        // rewrite module from request
        if (count($raw)) {
            $this->setParam('_module', array_shift($raw));
        }
        // rewrite module from controller
        if (count($raw)) {
            $this->setParam('_controller', array_shift($raw));
        }
        if ($size = count($raw)) {
            // save raw
            $this->rawParams = $raw;

            // save as index params
            foreach ($raw as $i => $value) {
                $this->setParam($i, $value);
            }

            // remove tail
            if ($size % 2 === 1) {
                array_pop($raw);
                $size = count($raw);
            }
            // or use array_chunk and run another loop?
            for ($i = 0; $i < $size; $i += 2) {
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
    protected function resetRequest(): void
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
}
