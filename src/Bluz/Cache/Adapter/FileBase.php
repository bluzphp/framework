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
namespace Bluz\Cache\Adapter;

use Bluz\Cache\InvalidArgumentException;

/**
 * Base class for all filesystem-based cache adapters
 * @todo http://habrahabr.ru/post/148527/#comment_5014715
 * @author murzik
 */
abstract class FileBase extends AbstractAdapter
{
    protected $cacheDir = null;
    protected $extension = ".bluzcache";

    /**
     * Check configuration and permissions
     *
     * @param array $settings
     * @throws \Bluz\Cache\InvalidArgumentException
     */
    public function __construct($settings = array())
    {
        if (!isset($settings['cacheDir'])) {
            throw new InvalidArgumentException("FileBase adapters is required 'cacheDir' option");
        }
        $cacheDir = $settings['cacheDir'];

        if (!is_dir($cacheDir)) {
            throw new InvalidArgumentException("'$cacheDir' is not directory");
        }

        if (!is_writable($cacheDir)) {
            throw new InvalidArgumentException("Directory '$cacheDir' is not writable");
        }
        // get rid of trailing slash
        $this->cacheDir = realpath($cacheDir);

        parent::__construct($settings);
    }

    /**
     * Flush implementation for all file-based cache implementations
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
     * @param string $id cache entry id
     * @return string path to file
     */
    protected function getFilename($id)
    {
        // Copypasted from Doctrine\Common\Cache\FileCache
        $path = implode(str_split(md5($id), 12), DIRECTORY_SEPARATOR);
        $path = $this->cacheDir . DIRECTORY_SEPARATOR . $path;

        return $path . DIRECTORY_SEPARATOR . $id . $this->extension;
    }

    /**
     * {@inheritdoc}
     */
    protected function doDelete($id)
    {
        return @unlink($this->getFilename($id));
    }
}