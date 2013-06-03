<?php
/**
 * Copyright (c) 2013 by Bluz PHP Team
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
namespace Bluz\Config;

/**
 * Config
 *
 * @category Bluz
 * @package  Config
 *
 * @author   Anton Shevchuk
 * @created  03.03.12 14:03
 */
class Config
{
    use \Bluz\Package;

    /**
     * @var array
     */
    protected $config;

    /**
     * Path to configuration files
     * @var string
     */
    protected $path;

    /**
     * setup path to configuration files
     *
     * @param $path
     * @throws ConfigException
     * @return self
     */
    public function setPath($path)
    {
        if (!is_dir($path)) {
            throw new ConfigException('Configuration directory is not exists');
        }
        $this->path = rtrim($path, '/');
    }

    /**
     * load
     *
     * @param string $environment
     * @throws ConfigException
     * @return bool
     */
    public function load($environment = null)
    {
        if (!$this->path) {
            throw new ConfigException('Configuration directory is not setup');
        }

        $configFile = $this->path . '/application.php';

        if (!is_file($configFile) or !is_readable($configFile)) {
            throw new ConfigException('Configuration file is not found');
        }

        // TODO: or need without "once" for multi application
        $this->config = require $configFile;
        if (null !== $environment) {
            $customConfig = $this->path . '/app.' . $environment . '.php';
            if (is_file($customConfig) && is_readable($customConfig)) {
                $customConfig = require $customConfig;
                $this->config = array_replace_recursive($this->config, $customConfig);
            }
        }
    }

    /**
     * __get
     *
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        if (isset($this->config[$key])) {
            return $this->config[$key];
        } else {
            return null;
        }
    }

    /**
     * __set
     *
     * @param $key
     * @param $value
     * @throws ConfigException
     * @return void
     */
    public function __set($key, $value)
    {
        throw new ConfigException('Configuration is read only');
    }

    /**
     * __isset
     *
     * @param $key
     * @return boolean
     */
    public function __isset($key)
    {
        return isset($this->config[$key]);
    }

    /**
     * return configuration as array
     *
     * @param string|null $section of config
     * @param string|null $subsection of config
     * @throws ConfigException
     * @return array
     */
    public function getData($section = null, $subsection = null)
    {
        if (!$this->config) {
            throw new ConfigException('System configuration is missing');
        }

        if (null !== $section && isset($this->config[$section])) {
            if ($subsection
                && isset($this->config[$section][$subsection])
            ) {
                return $this->config[$section][$subsection];
            } else {
                return $this->config[$section];
            }

        } elseif (null !== $section) {
            return null;
        } else {
            return $this->config;
        }
    }
}