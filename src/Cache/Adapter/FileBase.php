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
namespace Bluz\Cache\Adapter;

use Bluz\Common\Exception\ConfigurationException;

/**
 * Base class for all filesystem-based cache adapters
 *
 * @todo http://habrahabr.ru/post/148527/#comment_5014715
 *
 * @package Bluz\Cache\Adapter
 * @author  murzik
 */
abstract class FileBase extends AbstractAdapter
{
    /**
     * Directory of cache files
     * @var null|string
     */
    protected $cacheDir = null;

    /**
     * Extension of cache files
     * @var string
     */
    protected $extension = ".bluzcache";

    /**
     * Check configuration and permissions
     *
     * @param array $settings
     * @throws ConfigurationException
     */
    public function __construct($settings = array())
    {
        if (!isset($settings['cacheDir'])) {
            throw new ConfigurationException("FileBase adapters is required 'cacheDir' option");
        }
        $cacheDir = $settings['cacheDir'];

        if (!is_dir($cacheDir)) {
            throw new ConfigurationException("'$cacheDir' is not directory");
        }

        if (!is_writable($cacheDir)) {
            throw new ConfigurationException("Directory '$cacheDir' is not writable");
        }
        // get rid of trailing slash
        $this->cacheDir = realpath($cacheDir);

        parent::__construct($settings);
    }

    /**
     * Flush implementation for all file-based cache implementations
     *
     * @return bool
     */
    protected function doFlush()
    {
        // copypaste from Doctrine\Common\Cache\FileCache.
        $pattern = '/^.+\\' . $this->extension . '$/i';
        $iterator = new \RecursiveDirectoryIterator($this->cacheDir);
        $iterator = new \RecursiveIteratorIterator($iterator);
        $iterator = new \RegexIterator($iterator, $pattern);

        foreach ($iterator as $name => $file) {
            @unlink($name);
        }

        return true;
    }

    /**
     * Generate path to cache file based on cache entry id
     *
     * @param string $id cache entry id
     * @return string path to file
     */
    protected function getFilename($id)
    {
        // make uid as hash from id
        // split it by 4 chars for make directory structure
        $path = join(DIRECTORY_SEPARATOR, str_split(md5($id), 4));
        $path = $this->cacheDir . DIRECTORY_SEPARATOR . $path;

        return $path . $this->extension;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $id
     * @return bool|mixed
     */
    protected function doDelete($id)
    {
        return @unlink($this->getFilename($id));
    }
}
