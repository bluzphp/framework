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

namespace Bluz\Cache;
use Bluz\Cache\CacheInterface;
use Bluz\Cache\TagableInterface;
use Bluz\Cache\CacheException;
/**
 * Cache Frontend for Bluz\Cache system
 * @author murzik
 */
class Cache implements CacheInterface,TagableInterface
{
    /**
     * @var \Bluz\Cache\AdapterBase
     */
    protected $cacheDriver = null;
    /**
     * @var \Bluz\Cache\AdapterBase
     */
    protected $tagDriver = null;

    protected $tagPrefix = '__tag__';

    protected $enabled = true;


    /**
     * Cache frontend constructor
     * @param array $options Available options are:
     *  - 'enabled' => Boolean,optional, true by default
     *  - 'cacheAdapter' => Instance of Bluz\Cache\CacheInterface. Required if option 'enabled' set to true
     *  - 'tagAdapter' => Instance of Bluz\Cache\CacheInterface. Optional.
     *                    If it is not set, 'cacheAdapter' instance will be used as a tag adapter
     * @throws Bluz\Cache\InvalidArgumentException if cache enabled and 'cacheAdapter' option is missing
     */
    public function __construct($options = array())
    {
        if(isset($options['enabled'])) {
            $this->enabled = (bool) $options['enabled'];
        }

        //if cache disabled by default - we can fall through cacheAdapter check
        if( ! isset($options['cacheAdapter']) && $this->enabled) {
            $msg = "Missing cacheAdapter configuration option";
            throw new InvalidArgumentException($msg);
        }

        $this->setCacheAdapter($options['cacheAdapter']);

        if( ! isset($options['tagAdapter'])) {
            $this->setTagAdapter($options['cacheAdapter']);
        } else {
            $this->setTagAdapter($options['tagAdapter']);
        }


    }

    /**
     * Enable/Disable cache.
     * If cache is disabled, any calls to Bluz\Cache\CacheInterface methods will do nothing.
     * Note that you can't enable Bluz\Cache if cacheAdapter not set
     * @param bool $flag [OPTIONAL] default to true
     * @throws Bluz\CacheException during attempt to enable misconfigured \Bluz\Cache\Cache instance
     */
    public function setEnabled($flag = true)
    {
        $flag = (bool) $flag;
        if( ! $this->enabled && $flag) {

            if( ! $this->cacheDriver) {
                //we going to enable cache. we can't do that if there is no cache adapter instance
                $msg = "You can't enable cache. Cache adapter is missing.
                        Use \\Bluz\\Cache#setCacheAdapter method to set adapter instance";
                throw new CacheException($msg);
            }

            if( ! $this->tagDriver) {
                //use cacheDriver for tagable if it is missing
                $this->tagDriver = $this->cacheDriver;
            }

        }

        $this->enabled = (bool) $flag;
    }

    public function setCacheAdapter(CacheInterface $adapter)
    {
        $this->cacheDriver = $adapter;
    }

    public function setTagAdapter(CacheInterface $adapter)
    {
        $this->tagDriver = $adapter;
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
        if( ! $this->enabled) {
            return false;
        }

        return $this->cacheDriver->get($id);
    }

    /**
     * {@inheritdoc}
     */
    public function add($id, $data, $ttl = 0)
    {
        if( ! $this->enabled) {
            return false;
        }

        return $this->cacheDriver->add($id, $data, $ttl);
    }

    /**
     * {@inheritdoc}
     */
    public function set($id, $data, $ttl = 0)
    {
        if( ! $this->enabled) {
            return false;
        }

        return $this->cacheDriver->set($id, $data, $ttl);
    }

    /**
     * {@inheritdoc}
     */
    public function contains($id)
    {
        if( ! $this->enabled) {
            return false;
        }

        return $this->cacheDriver->contains($id);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        if( ! $this->enabled) {
            return false;
        }

        return $this->cacheDriver->delete($id);
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        if( ! $this->enabled) {
            return false;
        }

        return $this->cacheDriver->flush();
    }

    /**
     * Get underlying cache driver
     * @return AdapterBase
     */
    public function getAdapter()
    {
        return $this->cacheDriver;
    }

    /**
     * {@inheritdoc}
     */
    public function addTag($id, $tag)
    {
        if( ! $this->enabled) {
            return false;
        }

        $identifiers = array();
        $tag = $this->tagPrefix . $tag;

        if($this->tagDriver->contains($tag)) {
            $identifiers = $this->tagDriver->get($tag);
        }

        //array may contain not unique values, but I can't see problem here
        $identifiers[] = $id;

        return $this->tagDriver->set($tag, $identifiers);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByTag($tag)
    {
        if( ! $this->enabled) {
            return false;
        }

        //maybe it makes sense to add check for prefix existence in tag name
        $tag = $this->tagPrefix . $tag;
        $identifiers  = $this->tagDriver->get($tag);

        if( ! $identifiers) {
            return false;
        }

        foreach($identifiers as $identifier) {
            $this->cacheDriver->delete($identifier);
        }

        //TODO: m-m-m-m..... not sure about line below. Do we need this?
//        $this->tagDriver->delete($tag);

        return true;
    }

}