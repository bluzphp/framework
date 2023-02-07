<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Router;

use Bluz\Common\Options;
use Bluz\Proxy\Application;
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
    public const DEFAULT_MODULE = 'index';
    public const DEFAULT_CONTROLLER = 'index';
    public const ERROR_MODULE = 'error';
    public const ERROR_CONTROLLER = 'index';

    /**
     * @var string default module
     */
    protected string $defaultModule = self::DEFAULT_MODULE;

    /**
     * @var string default Controller
     */
    protected string $defaultController = self::DEFAULT_CONTROLLER;

    /**
     * @var string error module
     */
    protected string $errorModule = self::ERROR_MODULE;

    /**
     * @var string error Controller
     */
    protected string $errorController = self::ERROR_CONTROLLER;

    /**
     * @var array instance parameters
     */
    protected array $params = [];

    /**
     * @var array instance raw parameters
     */
    protected array $rawParams = [];

    /**
     * @var string base URL
     */
    protected string $baseUrl;

    /**
     * @var string clean path without base URL
     */
    protected string $path;

    /**
     * @var array routers map module/controller => route
     */
    protected array $modules;

    /**
     * @var array reverse map route => module/controller
     */
    protected array $routes;

    /**
     * Constructor of Router
     */
    public function __construct(
        string $baseUrl,
        string $path,
        ?array $modules,
        ?array $routes,
    ) {
        $this->baseUrl = $baseUrl;
        $this->path = $path;
        $this->modules = $modules ?? [];
        $this->routes = $routes ?? [];
    }

    /**
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Get an action parameter
     *
     * @param mixed $key
     * @param mixed|null $default Default value to use if key not found
     *
     * @return mixed
     */
    public function getParam(mixed $key, mixed $default = null): mixed
    {
        return $this->params[$key] ?? $default;
    }

    /**
     * Set an action parameter
     *
     * A $value of null will unset the $key if it exists
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return void
     */
    public function setParam(mixed $key, mixed $value): void
    {
        $key = (string)$key;

        if (null === $value) {
            unset($this->params[$key]);
        } else {
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
     * Build URL to controller
     *
     * @param string $module
     * @param string $controller
     * @param array $params
     *
     * @return string
     */
    public function getUrl(
        string $module = self::DEFAULT_MODULE,
        string $controller = self::DEFAULT_CONTROLLER,
        array $params = []
    ): string {
        if (isset($this->modules[$module][$controller]['@']['route'])) {
            return $this->urlCustom($module, $controller, $params);
        }

        return $this->urlRoute($module, $controller, $params);
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
        $url = $this->modules[$module][$controller]['@']['route'];

        $getParams = [];
        foreach ($params as $key => $value) {
            // sub-array as GET params
            if (is_array($value)) {
                $getParams[$key] = $value;
                continue;
            }
            if (is_numeric($value)) {
                $value = (string)$value;
            }
            $url = str_replace('{$' . $key . '}', $value, $url, $replaced);
            // if not replaced, setup param as GET
            if (!$replaced) {
                $getParams[$key] = $value;
            }
        }
        // clean optional params
        $url = preg_replace('/\{\$[a-z0-9]+}/i', '', $url);
        // clean regular expression (.*)
        $url = preg_replace('/\(\.\*\)/', '', $url);
        // replace "//" with "/"
        $url = str_replace('//', '/', $url);

        if (!empty($getParams)) {
            $url .= '?' . http_build_query($getParams);
        }
        return $this->baseUrl . ltrim($url, '/');
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
        $url = $this->baseUrl;

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
    }

    /**
     * Process default router
     *
     * @return bool
     */
    protected function processDefault(): bool
    {
        return empty($this->path);
    }

    /**
     * Process custom router
     *
     * @return bool
     */
    protected function processCustom(): bool
    {
        $uri = '/' . $this->path;
        foreach ($this->routes as $pattern => $route) {
            if (preg_match($pattern, $uri, $matches)) {
                $this->setParam('_module', $route['module']);
                $this->setParam('_controller', $route['controller']);

                foreach ($route['params'] as $param => $type) {
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
        $uri = $this->path;
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
}
