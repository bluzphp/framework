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
class Cache implements CacheInterface, TagableInterface
{
    use \Bluz\Package;

    /**
     * @var AbstractAdapter
     */
    protected $cacheAdapter = null;

    /**
     * @var AbstractAdapter
     */
    protected $tagAdapter = null;

    protected $tagPrefix = '__tag__';

    protected $enabled = true;

    /**
     * Enable/Disable cache.
     * If cache is disabled, any calls to Bluz\Cache\CacheInterface methods will do nothing.
     * Note that you can't enable Bluz\Cache if cacheAdapter not set
     * @param bool $flag [OPTIONAL] default to true
     * @throws CacheException during attempt to enable misconfigured \Bluz\Cache\Cache instance
     */
    public function setEnabled($flag = true)
    {
        $this->enabled = (bool)$flag;
    }

    /**
     * setSettings
     *
     * @param array $settings
     * @return self
     */
    public function setSettings($settings = array())
    {
        return $this;
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
     * Check whether cache enabled or not
     * @return bool $isEnabled
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        if (!$this->enabled) {
            return false;
        }

        return $this->cacheAdapter->get($id);
    }

    /**
     * {@inheritdoc}
     */
    public function add($id, $data, $ttl = 0)
    {
        if (!$this->enabled) {
            return false;
        }

        return $this->cacheAdapter->add($id, $data, $ttl);
    }

    /**
     * {@inheritdoc}
     */
    public function set($id, $data, $ttl = 0)
    {
        if (!$this->enabled) {
            return false;
        }

        return $this->cacheAdapter->set($id, $data, $ttl);
    }

    /**
     * {@inheritdoc}
     */
    public function contains($id)
    {
        if (!$this->enabled) {
            return false;
        }

        return $this->cacheAdapter->contains($id);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        if (!$this->enabled) {
            return false;
        }

        return $this->cacheAdapter->delete($id);
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        if (!$this->enabled) {
            return false;
        }

        return $this->cacheAdapter->flush();
    }

    /**
     * Get underlying cache Adapter
     * @return AdapterBase
     */
    public function getAdapter()
    {
        return $this->cacheAdapter;
    }

    /**
     * {@inheritdoc}
     */
    public function addTag($id, $tag)
    {
        if (!$this->enabled) {
            return false;
        }

        $identifiers = array();
        $tag = $this->tagPrefix . $tag;

        if ($this->tagAdapter->contains($tag)) {
            $identifiers = $this->tagAdapter->get($tag);
        }

        // array may contain not unique values, but I can't see problem here
        $identifiers[] = $id;

        return $this->tagAdapter->set($tag, $identifiers);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByTag($tag)
    {
        if (!$this->enabled) {
            return false;
        }

        // maybe it makes sense to add check for prefix existence in tag name
        $tag = $this->tagPrefix . $tag;
        $identifiers = $this->tagAdapter->get($tag);

        if (!$identifiers) {
            return false;
        }

        foreach ($identifiers as $identifier) {
            $this->cacheAdapter->delete($identifier);
        }

        // TODO: m-m-m-m..... not sure about line below. Do we need this?
//        $this->tagAdapter->delete($tag);

        return true;
    }

}