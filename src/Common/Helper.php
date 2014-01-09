<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Common;

use Bluz\Common\Exception;

/**
 * Application
 *
 * @category Bluz
 * @package  Common
 *
 * @author   Anton Shevchuk
 * @created  18.07.12 14:46
 */
trait Helper
{
    /**
     * @var array
     */
    protected $helpers = array();

    /**
     * @var array
     */
    protected static $helpersPath = array();

    /**
     * Set application helpers
     *
     * @param array $helpers
     * @return self
     */
    public function setHelpers($helpers)
    {
        foreach ($helpers as $name => $function) {
            $this->helpers[$name] = $function;
        }
        return $this;
    }

    /**
     * Set application helpers path
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
     * Add view helper path
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
     * Call
     *
     * @param string $method
     * @param array $args
     * @throws Exception
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (isset($this->helpers[$method])
            && $this->helpers[$method] instanceof \Closure
        ) {
            return call_user_func_array($this->helpers[$method], $args);
        }

        // try to load Helpers inside class directory
        /* if (!self::$helpersPath) {
            $path = app()->getLoader()->findFile(__CLASS__);
            $this->addHelperPath(dirname($path) . '/Helper/');
        }*/

        foreach (self::$helpersPath as $helperPath) {
            $helperPath = realpath($helperPath . '/' . ucfirst($method) . '.php');
            if ($helperPath) {
                $helperInclude = include $helperPath;
                if ($helperInclude instanceof \Closure) {
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
