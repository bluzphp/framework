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
namespace Bluz\View;

use Bluz\Application;

/**
 * Cache for View
 *
 * @TODO: move to Cache package
 *
 * @category Bluz
 * @package  View
 *
 * @author   Anton Shevchuk
 * @created  16.08.12 13:09
 */
class Cache
{
    use \Bluz\Package;

    /**
     * Global cache flag
     *
     * @var boolean
     */
    protected $cache = false;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $file;

    /**
     * @var string
     */
    protected $fullPath;

    /**
     * setCache
     *
     * @param boolean $flag
     * @return View
     */
    public function setCache($flag)
    {
        $this->cache = (boolean) $flag;
        return $this;
    }

    /**
     * setCachePath
     *
     * @param string $path
     * @throws ViewException
     * @return View
     */
    public function setPath($path)
    {
        if (!is_dir($path) or !is_writable($path)) {
            throw new ViewException('View: Cache path is not writable');
        }
        $this->path = $path;
        return $this;
    }

    /**
     * start cache
     *
     * @param string  $file
     * @param array   $keys
     * @param integer $ttl in minutes
     * @return View
     */
    public function load($file, $keys = array(), $ttl = 5)
    {
        if (!$this->cache) {
            return false;
        }

        $key = md5(serialize($keys)) .'.html';

        $this->fullPath = $this->path.'/'.$file.'/'.$key;

        if (is_file($this->fullPath) && (filemtime($this->fullPath) > (time() - $ttl*60))) {
            return $this;
        } else {
            // create new cache
            // clean old cache
            if (is_file($this->fullPath)) {
                @unlink($this->fullPath);
            }
            return false;
        }
    }

    /**
     * @return void
     */
    private function createDir()
    {
        $path = substr($this->fullPath, 0, strrpos($this->fullPath, '/')) .'/';

        // use or create path recursive
        is_dir($path) || mkdir($path, 0755, true);
        @chmod($path, 0755);
    }

    /**
     * @todo http://habrahabr.ru/post/148527/#comment_5014715
     * @param $content
     * @return void
     */
    public function save($content)
    {
        $this->createDir();
        file_put_contents($this->fullPath, $content);
        @chmod($this->fullPath, 0755);
    }

    /**
     * is callable
     *
     * @return string
     */
    public function __toString()
    {
        return file_get_contents($this->fullPath);
    }
}