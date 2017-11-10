<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Config;

use Bluz\Common\Collection;

/**
 * Config
 *
 * @package  Bluz\Config
 * @author   Anton Shevchuk
 * @link     https://github.com/bluzphp/framework/wiki/Config
 */
class Config
{
    /**
     * @var array configuration data
     */
    protected $config;

    /**
     * @var array modules configuration data
     */
    protected $modules;

    /**
     * @var string path to configuration files
     */
    protected $path;

    /**
     * @var string environment
     */
    protected $environment;

    /**
     * Set path to configuration files
     *
     * @param  string $path
     *
     * @return void
     * @throws ConfigException
     */
    public function setPath($path) : void
    {
        if (!is_dir($path)) {
            throw new ConfigException('Configuration directory is not exists');
        }
        $this->path = rtrim($path, '/');
    }

    /**
     * Set application environment
     *
     * @param  string $environment
     *
     * @return void
     */
    public function setEnvironment($environment) : void
    {
        $this->environment = $environment;
    }

    /**
     * Load configuration
     *
     * @return void
     * @throws ConfigException
     */
    public function init() : void
    {
        if (!$this->path) {
            throw new ConfigException('Configuration directory is not setup');
        }

        $this->config = $this->loadFiles($this->path . '/configs/default');

        if ($this->environment) {
            $customConfig = $this->loadFiles($this->path . '/configs/' . $this->environment);
            $this->config = array_replace_recursive($this->config, $customConfig);
        }
    }

    /**
     * Load configuration file
     *
     * @param  string $path
     *
     * @return array
     * @throws ConfigException
     */
    protected function loadFile($path)
    {
        if (!is_file($path) && !is_readable($path)) {
            throw new ConfigException("Configuration file `$path` not found");
        }
        return include $path;
    }

    /**
     * Load configuration files to array
     *
     * @param  string $path
     *
     * @return array
     * @throws ConfigException
     */
    protected function loadFiles($path) : array
    {
        $config = [];

        if (!is_dir($path)) {
            throw new ConfigException("Configuration directory `$path` not found");
        }

        $iterator = new \GlobIterator(
            $path . '/*.php',
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
     *
     * @param array $keys
     *
     * @return array|mixed
     * @throws ConfigException
     */
    public function getData(...$keys)
    {
        // configuration is missed
        if (null === $this->config) {
            throw new ConfigException('System configuration is missing');
        }

        if (!count($keys)) {
            return $this->config;
        }

        return Collection::get($this->config, ...$keys);
    }

    /**
     * Return module configuration by section
     *
     * @param  string $module
     * @param  string $section
     *
     * @return mixed
     * @throws \Bluz\Config\ConfigException
     */
    public function getModuleData($module, $section = null)
    {
        if (!isset($this->modules[$module])) {
            $this->modules[$module] = $this->loadFile(
                $this->path . '/modules/' . $module . '/config.php'
            );

            if (null === $this->config) {
                $this->init();
            }

            if (isset($this->config["module.$module"])) {
                $this->modules[$module] = array_replace_recursive(
                    $this->modules[$module],
                    $this->config["module.$module"]
                );
            }
        }

        if (null !== $section) {
            return $this->modules[$module][$section] ?? null;
        }

        return $this->modules[$module];
    }
}
