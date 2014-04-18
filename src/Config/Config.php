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
namespace Bluz\Config;

use Bluz\Common\Options;

/**
 * Config
 *
 * @package  Bluz\Config
 *
 * @author   Anton Shevchuk
 * @created  03.03.12 14:03
 */
class Config
{
    use Options;

    /**
     * Configuration data
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
