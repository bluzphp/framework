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
     * @param string $path
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
     * @return void
     */
    public function init($environment = null)
    {
        if (!$this->path) {
            throw new ConfigException('Configuration directory is not setup');
        }

        $this->config = $this->loadFiles($this->path .'/default');

        if (!is_null($environment)) {
            $customConfig = $this->loadFiles($this->path . '/' . $environment);
            $this->config = array_replace_recursive($this->config, $customConfig);
        }
    }

    /**
     * Load configuration files to array
     *
     * @param string $path
     * @throws ConfigException
     * @return array
     */
    protected function loadFiles($path)
    {
        $config = array();

        if (!is_dir($path)) {
            throw new ConfigException('Configuration directory `'.$path.'` not found');
        }

        $iterator = new \GlobIterator(
            $path .'/*.php',
            \FilesystemIterator::KEY_AS_FILENAME | \FilesystemIterator::CURRENT_AS_PATHNAME
        );

        foreach ($iterator as $name => $file) {
            $name = substr($name, 0, -4);
            $config[$name] = include $file;
        }
        return $config;
    }

    /**
     * __get
     *
     * @param string $key
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
     * @param string $key
     * @param mixed $value
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
     * @param string $key
     * @return bool
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
     * @return array|mixed
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
