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

/**
 * Config
 *
 * @package  Bluz\Config
 * @link     https://github.com/bluzphp/framework/wiki/Config
 *
 * @author   Anton Shevchuk
 * @created  03.03.12 14:03
 */
class Config
{
    /**
     * @var array Configuration data
     */
    protected $config;

    /**
     * @var array Modules configuration data
     */
    protected $modules;

    /**
     * @var string Path to configuration files
     */
    protected $path;

    /**
     * @var string Environment
     */
    protected $environment;

    /**
     * Set path to configuration files
     *
     * @param string $path
     * @throws ConfigException
     * @return void
     */
    public function setPath($path)
    {
        if (!is_dir($path)) {
            throw new ConfigException('Configuration directory is not exists');
        }
        $this->path = rtrim($path, '/');
    }

    /**
     * Set application environment
     *
     * @param string $environment
     * @return void
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;
    }

    /**
     * Load configuration
     * @throws ConfigException
     * @return void
     */
    public function init()
    {
        if (!$this->path) {
            throw new ConfigException('Configuration directory is not setup');
        }

        $this->config = $this->loadFiles($this->path .'/configs/default');

        if ($this->environment) {
            $customConfig = $this->loadFiles($this->path . '/configs/' . $this->environment);
            $this->config = array_replace_recursive($this->config, $customConfig);
        }
    }

    /**
     * Load configuration file
     * @param string $path
     * @throws ConfigException
     * @return array
     */
    protected function loadFile($path)
    {
        if (!is_file($path) && !is_readable($path)) {
            throw new ConfigException('Configuration file `'.$path.'` not found');
        }
        return include $path;
    }

    /**
     * Load configuration files to array
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
            $config[$name] = $this->loadFile($file);
        }
        return $config;
    }

    /**
     * Return configuration by key
     * @api
     * @param string|null $key of config
     * @param string|null $section of config
     * @throws ConfigException
     * @return array|mixed
     */
    public function getData($key = null, $section = null)
    {
        // configuration is missed
        if (is_null($this->config)) {
            throw new ConfigException('System configuration is missing');
        }

        // return all configuration
        if (is_null($key)) {
            return $this->config;
        }

        // return part of configuration
        if (isset($this->config[$key])) {
            // return section of configuration
            if (!is_null($section)
                && isset($this->config[$key][$section])
            ) {
                return $this->config[$key][$section];
            } else {
                return $this->config[$key];
            }
        } else {
            return null;
        }
    }

    /**
     * Return module configuration by section
     * @api
     * @param string $module
     * @param null $section
     * @return mixed
     */
    public function getModuleData($module, $section = null)
    {
        if (!isset($this->modules[$module])) {
            $this->modules[$module] = $this->loadFile(
                $this->path .'/modules/'. $module .'/config.php'
            );

            if (is_null($this->config)) {
                $this->init();
            }

            if (isset($this->config['module.'. $module])) {
                $this->modules[$module] = array_replace_recursive(
                    $this->modules[$module],
                    $this->config['module.'. $module]
                );
            }
        }

        if (!is_null($section)) {
            if (isset($this->modules[$module][$section])) {
                return $this->modules[$module][$section];
            } else {
                return null;
            }
        } else {
            return $this->modules[$module];
        }
    }
}
