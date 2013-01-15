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
 * Cache Frontend for Bluz\Cache system
 *
 *  - 'enabled' => Boolean,optional, true by default
 *  - 'settings' =>
 *     - 'cacheAdapter' => Settings for setup Instance of Bluz\Cache\CacheInterface. Required if option 'enabled' set to true
 *     - 'tagAdapter' => Settings fir setup Instance of Bluz\Cache\CacheInterface. Optional.
 *                    If it is not set, 'cacheAdapter' instance will be used as a tag adapter
 *
 * @author murzik
 */
use Bluz\Cache\Adapter;
use Bluz\Config\ConfigException;

class Cache implements CacheInterface, TagableInterface
{
    use \Bluz\Package;

    /**
     * @var Adapter\AbstractAdapter
     */
    protected $cacheAdapter = null;

    /**
     * @var Adapter\AbstractAdapter
     */
    protected $tagAdapter = null;

    protected $tagPrefix = '__tag__';

    /**
     * check Cache configuration
     *
     * @throws \Bluz\Config\ConfigException
     * @return boolean
     */
    protected function checkOptions()
    {
        // check cache Adapter instance and settings for initialize it
        if (!isset($this->options['cacheAdapter']) && !isset($this->options['settings']['cacheAdapter'])) {
            throw new ConfigException(
                "Missed `cacheAdapter` option in `cache` configuration. <br/>\n".
                "Read more: <a href='https://github.com/bluzphp/framework/wiki/Cache'>https://github.com/bluzphp/framework/wiki/Cache</a>"
            );
        }
        return true;
    }

    /**
     * @param CacheInterface $adapter
     */
    public function setCacheAdapter(CacheInterface $adapter)
    {
        $this->cacheAdapter = $adapter;
    }

    /**
     * @param CacheInterface $adapter
     */
    public function setTagAdapter(CacheInterface $adapter)
    {
        $this->tagAdapter = $adapter;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        return $this->getAdapter()->get($id);
    }

    /**
     * {@inheritdoc}
     */
    public function add($id, $data, $ttl = 0)
    {
        return $this->getAdapter()->add($id, $data, $ttl);
    }

    /**
     * {@inheritdoc}
     */
    public function set($id, $data, $ttl = 0)
    {
        return $this->getAdapter()->set($id, $data, $ttl);
    }

    /**
     * {@inheritdoc}
     */
    public function contains($id)
    {
        return $this->getAdapter()->contains($id);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        return $this->getAdapter()->delete($id);
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        return $this->getAdapter()->flush();
    }

    /**
     * Get underlying cache Adapter
     * @return Adapter\AbstractAdapter
     */
    public function getAdapter()
    {
        if (!$this->cacheAdapter) {
            $this->cacheAdapter = $this->initAdapter($this->options['settings']['cacheAdapter']);
        }

        return $this->cacheAdapter;
    }

    /**
     * Get underlying cache TagAdapter
     * @throws CacheException
     * @return Adapter\AbstractAdapter
     */
    public function getTagAdapter()
    {
        if (!$this->tagAdapter) {
            // create instance of new adapter
            if (isset($this->options['settings']['tagAdapter'])) {
                $this->tagAdapter = $this->initAdapter($this->options['settings']['tagAdapter']);
            } elseif ($adapter = $this->getAdapter()) {
                $this->tagAdapter = $adapter;
            } else {
                throw new CacheException("Tag Adapter can't initialize. Configuration is missed");
            }
        }
        return $this->tagAdapter;
    }

    /**
     * initAdapter
     *
     * @param mixed $settings
     * @throws CacheException
     * @return Adapter\AbstractAdapter
     */
    protected function initAdapter($settings)
    {
        if (is_string($settings)) {
            $adapterName = $settings;
            $adapterSettings = [];
        } elseif (is_array($settings) && isset($settings['name']) && isset($settings['settings'])) {
            $adapterName = $settings['name'];
            $adapterSettings = $settings['settings'];
        } else {
            throw new CacheException("Cache Adapter can't initialize. Configuration is missed");
        }

        $adapterName = ucfirst($adapterName);
        $adapterClass = '\\Bluz\\Cache\\Adapter\\'.$adapterName;

        $adapter = new $adapterClass($adapterSettings);
        return $adapter;
    }

    /**
     * {@inheritdoc}
     */
    public function addTag($id, $tag)
    {
        $identifiers = array();
        $tag = $this->tagPrefix . $tag;

        if ($this->getTagAdapter()->contains($tag)) {
            $identifiers = $this->getTagAdapter()->get($tag);
        }

        // array may contain not unique values, but I can't see problem here
        $identifiers[] = $id;

        return $this->getTagAdapter()->set($tag, $identifiers);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByTag($tag)
    {
        // maybe it makes sense to add check for prefix existence in tag name
        $tag = $this->tagPrefix . $tag;
        $identifiers = $this->getTagAdapter()->get($tag);

        if (!$identifiers) {
            return false;
        }

        foreach ($identifiers as $identifier) {
            $this->getAdapter()->delete($identifier);
        }

        // TODO: m-m-m-m..... not sure about line below. Do we need this?
//        $this->tagAdapter->delete($tag);

        return true;
    }

}