<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Common;

use Bluz\Common\Exception\CommonException;

/**
 * Helper trait
 *
 * @package  Bluz\Common
 * @author   Anton Shevchuk
 * @link     https://github.com/bluzphp/framework/wiki/Trait-Helper
 */
trait Helper
{
    /**
     * @var array[] list of helpers
     */
    protected static $helpers = [];

    /**
     * @var array[] list of helpers paths
     */
    protected static $helpersPath = [];

    /**
     * Add helper callable
     *
     * @param  string $name
     * @param  string $path
     * @return void
     * @throws CommonException
     */
    private function addHelper(string $name, string $path)
    {
        $class = static::class;
        $path = realpath($path);

        if (!$path) {
            throw new CommonException("Helper `$name` not found for class `$class`");
        }

        // create store of helpers
        if (!isset(static::$helpers[$class])) {
            static::$helpers[$class] = [];
        }

        $helper = include $path;

        if (is_callable($helper)) {
            static::$helpers[$class][$name] = $helper;
        } else {
            throw new CommonException("Helper `$name` not found in file `$path`");
        }
    }

    /**
     * Call helper
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     * @throws CommonException
     */
    private function callHelper(string $name, array $arguments)
    {
        $class = static::class;
        if (isset(static::$helpers[$class], static::$helpers[$class][$name])) {
            /** @var \Closure $helper */
            $helper = static::$helpers[$class][$name];
            return $helper->call($this, ...$arguments);
        } else {
            throw new CommonException("Helper `$name` not registered for class `$class`");
        }
    }

    /**
     * Add helper path
     *
     * @param  string $path
     * @return void
     * @throws CommonException
     */
    public function addHelperPath(string $path)
    {
        $class = static::class;
        $path = realpath($path);

        if (!$path) {
            throw new CommonException("Invalid Helper path `$path` for class `$class`");
        }

        // create store of helpers
        if (!isset(static::$helpersPath[$class])) {
            static::$helpersPath[$class] = [];
        }

        if (!in_array($path, static::$helpersPath[$class])) {
            static::$helpersPath[$class][] = $path;
        }
    }

    /**
     * Set helpers path
     *
     * @param  array $helpersPath
     * @return void
     */
    public function setHelpersPath(array $helpersPath)
    {
        foreach ($helpersPath as $path) {
            $this->addHelperPath($path);
        }
    }

    /**
     * Call magic helper
     *
     * @param  string $method
     * @param  array  $args
     * @return mixed
     * @throws CommonException
     */
    public function __call($method, $args)
    {
        $class = static::class;

        // Call callable helper structure (function or class)
        if (isset(static::$helpers[$class], static::$helpers[$class][$method])) {
            return $this->callHelper($method, $args);
        }

        if (!isset(static::$helpersPath[$class])) {
            throw new CommonException("Helper path not found for class `$class`");
        }

        // Try to find helper file
        foreach (static::$helpersPath[$class] as $path) {
            if (realpath($path . '/' . ucfirst($method) . '.php')) {
                $this->addHelper($method, $path . '/' . ucfirst($method) . '.php');
                return $this->callHelper($method, $args);
            }
        }
        throw new CommonException("Helper `$method` not found for `$class`");
    }
}
