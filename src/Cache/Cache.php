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
namespace Bluz\Cache;

/**
 * Cache
 *
 * @category Bluz
 * @package  Cache
 *
 * @method get($key) variable from cache
 * @method add($key, $value, $ttl = 0)
 * @method set($key, $value, $ttl = 0)
 * @method delete($key, $time = 0)
 *
 * @author   Anton Shevchuk
 * @created  12.07.11 12:47
 */
class Cache
{
    use \Bluz\Package;

    /**
     * @var \Memcached
     */
    protected $memcached;

	/**
	 * Cache flag
	 * @var boolean
	 */
	protected $cache = true;

	/**
	 * Servers settings
	 * @var array
	 */
	protected $servers = array();

    /**
     * init handler
     *
     * @return \Memcached
     */
    public function handler()
    {
        if (!$this->cache or !sizeof($this->servers) or !class_exists('Memcached', false)) {
            return false;
        }

        if (!$this->memcached) {
            $this->memcached = new \Memcached();
            $this->memcached->addServers($this->servers);
        }
        return $this->memcached;
    }

    /**
     * setServers
     *
     * @param array $settings
     * @return Cache
     */
    public function setServers(array $settings)
    {
        $this->servers = $settings;
        return $this;
    }

    /**
     * setCache
     *
     * @param boolean $flag
     * @return Cache
     */
    public function setCache($flag)
    {
        $this->cache = $flag;
        return $this;
    }

    /**
     * Get data from cache
     * otherwise call callback function
     *
     * @param string   $key
     * @param \closure $callback
     * @param int      $ttl
     * @throws \Bluz\Exception
     * @return mixed
     */
    public function getData($key, $callback, $ttl = 0)
    {
        if (!is_callable($callback)) {
            throw new \Bluz\Exception('Callback parameter should be callable, like closure');
        }
        if (!$handler = $this->handler()) {
             return $callback();
        }

        if (!$data = $handler->get($key)) {
            $data = $callback();
            $handler->set($key, $data, $ttl);
        }
        return $data;
    }

    /**
     * __call
     *
     * @param string $method
     * @param array  $params
     * @throws CacheException
     * @return mixed
     */
    public function __call($method, $params)
    {
        if (!$handler = $this->handler()) {
            return false;
        }

        if (!method_exists($handler, $method)) {
            throw new CacheException('Method "'.$method.'" is not support by cache handler');
        }

        return call_user_func_array(array($handler, $method), $params);
    }
}