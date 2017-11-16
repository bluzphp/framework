<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
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
     * Add helper path
     *
     * @param  string $path
     *
     * @return void
     * @throws CommonException
     */
    public function addHelperPath(string $path) : void
    {
        $class = static::class;
        $realPath = realpath($path);

        if (false === $realPath) {
            throw new CommonException("Invalid Helper path `$path` for class `$class`");
        }

        // create store of helpers
        if (!isset(static::$helpersPath[$class])) {
            static::$helpersPath[$class] = [];
        }

        if (!in_array($realPath, static::$helpersPath[$class], true)) {
            static::$helpersPath[$class][] = $realPath;
        }
    }

    /**
     * Call magic helper
     *
     * @param  string $method
     * @param  array  $arguments
     *
     * @return mixed
     * @throws CommonException
     */
    public function __call($method, $arguments)
    {
        $class = static::class;

        // Call callable helper structure (function or class)
        if (!isset(static::$helpers[$class], static::$helpers[$class][$method])) {
            $this->loadHelper($method);
        }

        /** @var \Closure $helper */
        $helper = static::$helpers[$class][$method];
        return $helper->call($this, ...$arguments);
    }

    /**
     * Call helper
     *
     * @param string $name
     *
     * @return void
     * @throws CommonException
     */
    private function loadHelper(string $name) : void
    {
        $class = static::class;

        // Somebody forgot to call `addHelperPath`
        if (!isset(static::$helpersPath[$class])) {
            throw new CommonException("Helper path not found for class `$class`");
        }

        // Try to find helper file
        foreach (static::$helpersPath[$class] as $path) {
            if ($helperPath = realpath($path . '/' . ucfirst($name) . '.php')) {
                $this->addHelper($name, $helperPath);
                return;
            }
        }

        throw new CommonException("Helper `$name` not found for class `$class`");
    }

    /**
     * Add helper callable
     *
     * @param  string $name
     * @param  string $path
     *
     * @return void
     * @throws CommonException
     */
    private function addHelper(string $name, string $path) : void
    {
        $class = static::class;

        // create store of helpers for this class
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
}
