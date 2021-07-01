<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Config;

use FilesystemIterator;
use GlobIterator;

/**
 * Config
 *
 * @package  Bluz\Config
 * @author   Anton Shevchuk
 * @link     https://github.com/bluzphp/framework/wiki/Config
 */
class ConfigLoader
{
    /**
     * @var array configuration data
     */
    protected $config;

    /**
     * @var string path to configuration files
     */
    protected $path;

    /**
     * @var string environment
     */
    protected $environment;

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Set path to configuration files
     *
     * @param  string $path
     *
     * @return void
     * @throws ConfigException
     */
    public function setPath($path): void
    {
        if (!is_dir($path)) {
            throw new ConfigException('Configuration directory is not exists');
        }
        $this->path = rtrim($path, '/');
    }

    /**
     * @return string
     */
    public function getEnvironment(): string
    {
        return $this->environment;
    }

    /**
     * Set application environment
     *
     * @param  string $environment
     *
     * @return void
     */
    public function setEnvironment($environment): void
    {
        $this->environment = $environment;
    }

    /**
     * Load configuration
     *
     * @return void
     * @throws ConfigException
     */
    public function load(): void
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
     * @return mixed
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
    protected function loadFiles($path): array
    {
        $config = [];

        if (!is_dir($path)) {
            throw new ConfigException("Configuration directory `$path` not found");
        }

        $iterator = new GlobIterator(
            $path . '/*.php',
            FilesystemIterator::KEY_AS_FILENAME | FilesystemIterator::CURRENT_AS_PATHNAME
        );

        foreach ($iterator as $name => $file) {
            $name = substr($name, 0, -4);
            $config[$name] = $this->loadFile($file);
        }
        return $config;
    }
}
