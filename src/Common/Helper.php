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
    protected $helpersPath = array();

    /**
     * Add helper path
     * @param string $path
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
     * Call magic helper
     * @param string $method
     * @param array $args
     * @throws CommonException
     * @return mixed
     */
    public function __call($method, $args)
    {
        // Setup key
        $key = get_called_class() .':'. $method;

        // Call helper function (or class)
        if (isset($this->helpers[$key]) && is_callable($this->helpers[$key])) {
            return call_user_func_array($this->helpers[$key], $args);
        }

        // Try to find helper file
        foreach ($this->helpersPath as $helperPath) {
            $helperPath = realpath($helperPath . '/' . ucfirst($method) . '.php');
            if ($helperPath) {
                $helperInclude = include $helperPath;
                if (is_callable($helperInclude)) {
                    $this->helpers[$key] = $helperInclude;
                    return call_user_func_array($this->helpers[$key], $args);
                } else {
                    throw new CommonException("Helper '$method' not found in file '$helperPath'");
                }
            }
        }
        throw new CommonException("Helper '$method' not found for '" . __CLASS__ . "'");
    }
}
