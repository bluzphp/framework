<?php
/**
 * Copyright (c) 2012 by Bluz PHP Team
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * @namespace
 */
namespace Bluz;

use Bluz\Exception;

/**
 * Application
 *
 * @category Bluz
 * @package  Application
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
    protected static $staticHelpers = array();

    /**
     * @var array
     */
    protected static $helpersPath = array();


    /**
     * Set application helpers
     *
     * @param array $helpers
     * @return Application
     */
    public function setHelpers($helpers)
    {
        foreach ($helpers as $name => $function) {
            $this->helpers[$name] = $function;
        }
        return $this;
    }

    /**
     * Set application helpers
     *
     * @param array $helpers
     * @return Application
     */
    public function setStaticHelpers($helpers)
    {
        foreach ($helpers as $name => $function) {
            self::$staticHelpers[$name] = $function;
        }
        return $this;
    }

    /**
     * Set application helpers path
     *
     * @param string|array $helpersPath
     * @return Application
     */
    public function setHelpersPath($helpersPath)
    {
        if (is_array($helpersPath)) {
            foreach ($helpersPath as $path) {
                $this->addHelperPath((string) $path);
            }
        } else {
            $this->addHelperPath((string) $helpersPath);
        }
        return $this;
    }

    /**
     * Add view helper path
     *
     * @param string $path
     * @return Application
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
     * @param array  $args
     * @throws Exception
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (isset($this->helpers[$method])
            && $this->helpers[$method] instanceof \Closure) {
            return call_user_func_array($this->helpers[$method], $args);
        }
        if (self::$helpersPath) {
            foreach(self::$helpersPath as $helperPath) {
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
            throw new Exception("Helper '$method' not found for '". __CLASS__ ."'");
        }
    }

    /**
     * Call static
     *
     * @param string $method
     * @param array  $args
     * @throws Exception
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        if (isset(self::$staticHelpers[$method])
            && self::$staticHelpers[$method] instanceof \Closure) {
            return call_user_func_array(self::$staticHelpers[$method], $args);
        }
        if (self::$helpersPath) {
            foreach(self::$helpersPath as $helperPath) {
                $helperPath = realpath($helperPath . '/' . ucfirst($method) . '.php');
                if ($helperPath) {
                    $helperInclude = include $helperPath;
                    if ($helperInclude instanceof \Closure) {
                        self::$staticHelpers[$method] = $helperInclude;
                    } else {
                        throw new Exception("Helper '$method' not found in file '$helperPath'");
                    }
                    return self::__callStatic($method, $args);
                }
            }
            throw new Exception("Helper '$method' not found for '". __CLASS__ ."'");
        }
    }
}
