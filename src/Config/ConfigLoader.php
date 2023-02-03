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
    protected array $config = [];

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Load and merge configuration
     *
     * @param string $path
     * @return void
     * @throws ConfigException
     */
    public function load(string $path): void
    {
        if (!is_dir($path)) {
            throw new ConfigException('Configuration directory is not exists');
        }

        $this->config = array_replace_recursive($this->config, $this->loadFiles($path));
    }

    /**
     * Load configuration file
     *
     * @param string $path
     *
     * @return mixed
     * @throws ConfigException
     */
    protected function loadFile(string $path): mixed
    {
        if (!is_file($path) && !is_readable($path)) {
            throw new ConfigException("Configuration file `$path` not found");
        }
        return include $path;
    }

    /**
     * Load configuration files to array
     *
     * @param string $path
     *
     * @return array
     * @throws ConfigException
     */
    protected function loadFiles(string $path): array
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
