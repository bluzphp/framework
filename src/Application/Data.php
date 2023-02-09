<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Application;

use Bluz\Collection\Collection;
use Bluz\Container\Container;
use Bluz\Container\JsonSerialize;
use Bluz\Container\RegularAccess;
use GlobIterator;
use ReflectionException;
use SplFileInfo;

/**
 * Data
 *
 * @package  Bluz\Application
 * @author   Anton Shevchuk
 */
class Data implements \JsonSerializable
{
    use Container;
    use RegularAccess;
    use JsonSerialize;

    /**
     * @param string $path
     */
    public function __construct(
        protected string $path
    ) {
    }

    /**
     * @return void
     * @throws ApplicationException|ReflectionException
     */
    public function init(): void
    {
        $path = $this->path . '/modules/*/controllers/*.php';
        foreach (new GlobIterator($path) as $file) {
            /* @var SplFileInfo $file */
            $module = $file->getPathInfo()->getPathInfo()->getBasename();
            $controller = $file->getBasename('.php');

            /**
             * @var Closure $controllerClosure
             */
            $closure = include $file;

            if (!is_callable($closure)) {
                throw new ApplicationException("Controller is not callable '{$module}/{$controller}'");
            }

            $this->container['modules'][$module][$controller] = [];

            $reflection = new \ReflectionFunction($closure);

            $parameters = $reflection->getParameters();
            foreach ($parameters as $parameter) {
                $this->container['modules'][$module][$controller]['params'][$parameter->getName()] =
                    $parameter->getType()?->getName() ?? 'mixed';
            }

            $attributes = $reflection->getAttributes();
            foreach ($attributes as $attribute) {
                $name = strtolower(substr(strrchr($attribute->getName(), '\\'), 1));
                $argument = current($attribute->getArguments());

                if ($name === 'route') {
                    $this->prepareRoute(
                        route: $argument,
                        module: $module,
                        controller: $controller,
                        params: $this->container['modules'][$module][$controller]['params'] ?? []
                    );
                }
                $this->setAttribute($module, $controller, $name, $argument);
            }
        }
    }

    /**
     * @param string $module
     * @param string $controller
     * @param string $attribute
     * @param mixed $value
     * @return void
     */
    protected function setAttribute(string $module, string $controller, string $attribute, mixed $value): void
    {
        Collection::add($this->container, 'modules', $module, $controller, '@', $attribute, $value);
    }

    /**
     * @param string $module
     * @param string $controller
     * @param string $attribute
     * @return mixed|null
     */
    public function getAttribute(string $module, string $controller, string $attribute): mixed
    {
        return Collection::get($this->container, 'modules', $module, $controller, '@', $attribute);
    }

    /**
     * @param string $module
     * @param string $controller
     * @return mixed|null
     */
    public function getParams(string $module, string $controller): mixed
    {
        return Collection::get($this->container, 'modules', $module, $controller, 'params');
    }

    /**
     * @param string $module
     * @param string $controller
     * @return mixed|null
     */
    public function getRoute(string $module, string $controller): mixed
    {
        return $this->getAttribute($module, $controller, 'route');
    }

    /**
     * @return array|null
     */
    public function getRoutes(): ?array
    {
        return $this->get('routes');
    }

    /**
     * @return array|null
     */
    public function getModules(): ?array
    {
        return $this->get('modules');
    }

    /**
     * @param string $route
     * @param string $module
     * @param string $controller
     * @param array $params
     * @return void
     */
    protected function prepareRoute(string $route, string $module, string $controller, array $params = []): void
    {
        $pattern = $this->prepareRoutePattern($route, $params);
        $this->container['routes'][$pattern] = [
            'module' => $module,
            'controller' => $controller,
            'params' => $params
        ];
    }

    /**
     * Prepare Route pattern
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    protected function prepareRoutePattern(string $route, array $params): string
    {
        $pattern = str_replace('/', '\/', $route);

        foreach ($params as $param => $type) {
            $replace = match ($type) {
                'int' => "(?P<$param>[0-9]+)",
                'float' => "(?P<$param>[0-9.,]+)",
                default => "(?P<$param>[a-zA-Z0-9-_.]+)", // string, array, mixed, etc
            };
            $pattern = str_replace("{\$$param}", $replace, $pattern);
        }
        return '/^' . $pattern . '/i';
    }
}
