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

use Bluz\Cache\CacheException;

/**
 * Redis cache adapter
 * @author The-Who
 */
class Redis extends AbstractAdapter
{

    /**
     * @var \Redis
     */
    protected $redis = null;

    /**
     * Host
     * @var string
     */
    protected $host = "127.0.0.1";

    /**
     * Port
     * @var int
     */
    protected $port = 6379;

    /**
     * Timeout
     * @var null
     */
    protected $timeout = null;

    /**
     * Check and setup Redis server
     *
     * @param array $settings
     * @throws \Bluz\Cache\CacheException
     */
    public function __construct($settings = array())
    {
        if (!extension_loaded('redis')) {
            $msg = "Redis extension not installed/enabled.
                    Install and/or enable Redis extension [http://pecl.php.net/package/redis]. See phpinfo() for more information";
            throw new CacheException($msg);
        }

        // check settings
        if (!is_array($settings) or empty($settings) or !isset($settings['server'])) {
            throw new CacheException(
                "Redis configuration is missed.
                Please check 'cache' configuration section"
            );
        }

        // check server
        if (isset($settings['server'][0])) {
            $this->server = $settings['server'][0];
        }

        // check port
        if (isset($settings['server'][1])) {
            $this->port = $settings['server'][1];
        }

        // check timeout
        if (isset($settings['server'][2])) {
            $this->timeout = $settings['server'][2];
        }

        parent::__construct($settings);
    }

    /**
     * getHandler
     *
     * @return \Redis
     */
    public function getHandler()
    {
        if (!$this->redis) {
            $this->redis = new \Redis();

            //TODO: Maybe we should replace this string to $this->redis->connect($this->$settings['server'][0], .. [1], ..[2])?
            $this->redis->connect($this->server, $this->port, $this->timeout);

            if (isset($this->settings['options'])) {
                foreach ($this->settings['options'] as $key => $value) {
                    $this->redis->setOption($key, $value);
                }
            }
        }
        return $this->redis;
    }

    /**
     * {@inheritdoc}
     */
    protected function doGet($id)
    {
        return $this->getHandler()->get($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function doAdd($id, $data, $ttl = 0)
    {
        if (!$this->doContains($id)) {
            return $this->getHandler()->setex($id, $ttl, $data);
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function doSet($id, $data, $ttl = 0)
    {
        return $this->getHandler()->setex($id, $ttl, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function doContains($id)
    {
        return $this->getHandler()->exists($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function doDelete($id)
    {
        return $this->getHandler()->delete($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function doFlush()
    {
        return $this->getHandler()->flushAll();
    }
}
