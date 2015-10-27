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
     * @var array list of helpers
     */
    protected $helpers = array();

    /**
     * @var array list of helpers paths
     */
    protected $helpersPath = array();

    /**
     * Add helper path
     *
     * @param  string $path
     * @return self
     */
    public function addHelperPath($path)
    {
        $path = rtrim(realpath($path), '/');
        if (false !== $path && !in_array($path, $this->helpersPath)) {
            $this->helpersPath[] = $path;
        }

        return $this;
    }

    /**
     * Set helpers path
     *
     * @param  string|array $helpersPath
     * @return self
     */
    public function setHelpersPath($helpersPath)
    {
        if (is_array($helpersPath)) {
            foreach ($helpersPath as $path) {
                $this->addHelperPath((string)$path);
            }
        } else {
            $this->addHelperPath((string)$helpersPath);
        }
        return $this;
    }

    /**
     * Reset helpers path
     *
     * @return self
     */
    public function resetHelpersPath()
    {
        $this->helpersPath = [];
        return $this;
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
        // Setup key
        $key = static::class .':'. $method;

        // Call callable helper structure (function or class)
        if (isset($this->helpers[$key]) && is_callable($this->helpers[$key])) {
            return $this->helpers[$key](...$args);
        }

        // Try to find helper file
        foreach ($this->helpersPath as $helperPath) {
            $helperPath = realpath($helperPath . '/' . ucfirst($method) . '.php');
            if ($helperPath) {
                $helperInclude = include $helperPath;
                if (is_callable($helperInclude)) {
                    $this->helpers[$key] = $helperInclude;
                    return $this->helpers[$key](...$args);
                } else {
                    throw new CommonException("Helper '$method' not found in file '$helperPath'");
                }
            }
        }
        throw new CommonException("Helper '$method' not found for '" . __CLASS__ . "'");
    }
}
