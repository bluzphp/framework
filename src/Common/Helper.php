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

/**
 * Helper trait
 *
 * @package  Bluz\Common
 *
 * @author   Anton Shevchuk
 * @created  18.07.12 14:46
 */
trait Helper
{
    /**
     * @var array of helpers
     */
    protected $helpers = array();

    /**
     * @var array of helpers paths
     */
    protected static $helpersPath = array();

    /**
     * Add helper path
     *
     * @param string $path
     * @return self
     */
    public function addHelperPath($path)
    {
        $path = rtrim(realpath($path), '/');
        if (false !== $path && !in_array($path, self::$helpersPath)) {
            self::$helpersPath[] = $path;
        }

        return $this;
    }

    /**
     * Set helpers path
     *
     * @param string|array $helpersPath
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
     * Call
     *
     * @param string $method
     * @param array $args
     * @throws Exception
     * @return mixed
     */
    public function __call($method, $args)
    {
        // Call helper function (or class)
        if (isset($this->helpers[$method]) && is_callable($this->helpers[$method])) {
            return call_user_func_array($this->helpers[$method], $args);
        }

        // Try to find helper file
        foreach (self::$helpersPath as $helperPath) {
            $helperPath = realpath($helperPath . '/' . ucfirst($method) . '.php');
            if ($helperPath) {
                $helperInclude = include $helperPath;
                if (is_callable($helperInclude)) {
                    $this->helpers[$method] = $helperInclude;
                } else {
                    throw new Exception("Helper '$method' not found in file '$helperPath'");
                }
                return $this->__call($method, $args);
            }
        }
        throw new Exception("Helper '$method' not found for '" . __CLASS__ . "'");
    }
}
