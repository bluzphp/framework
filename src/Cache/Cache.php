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
namespace Bluz\Cache;

use Bluz\Cache\Adapter;
use Bluz\Common\Options;
use Bluz\Config\ConfigException;

/**
 * Cache Frontend for Bluz\Cache system
 *
 * Configuration
 *     'enabled' => Boolean,optional, true by default
 *     'cacheAdapter' => 'redis'   // Required if option 'enabled' set to true
 *     'tagAdapter' => 'memcached' // Optional
 *     'settings' =>
 *         'redis' => []           // Settings for setup Instance of Bluz\Cache\CacheInterface.
 *         'memcached' => []       // Settings for another cache adapter
 *
 * @package Bluz\Cache
 *
 * @link https://github.com/bluzphp/framework/wiki/Cache
 *
 * @author murzik
 */
class Cache implements CacheInterface, TagableInterface
{
    use Options;

    /**
     * No expiry TTL
     */
    const TTL_NO_EXPIRY = 0;

    /**
     * Instance of cache adapter
     * @var CacheInterface
     */
    protected $adapter = null;

    /**
     * @var string Cache prefix
     */
    protected $prefix = 'bluz:';

    /**
     * Instance of tag adapter
     * @var CacheInterface
     */
    protected $tagAdapter = null;

    /**
     * Prefix for tags
     * @var string
     */
    protected $tagPrefix = 'bluz:@:';

    /**
     * Check Cache configuration
     *
     * @throws \Bluz\Config\ConfigException
     * @return bool
     */
    protected function checkOptions()
    {
        // check cache Adapter instance and settings for initialize it
        if (!$this->getOption('adapter')) {
            throw new ConfigException(
                "Missed `adapter` option in cache `settings` configuration. <br/>\n" .
                "Read more: <a href='https://github.com/bluzphp/framework/wiki/Cache'>".
                "https://github.com/bluzphp/framework/wiki/Cache</a>"
            );
        }
        return true;
    }

    /**
     * Prepare Id with prefix
     *
     * @param $id
     * @throws InvalidArgumentException
     * @return string
     */
    protected function prepareId($id)
    {
        return $this->prefix . $id;
    }

    /**
     * Setup prefix for cache records
     *
     * @param string $prefix
     * @return void
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * Setup prefix for cache records of tags
     *
     * @param string $prefix
     * @return void
     */
    public function setTagPrefix($prefix)
    {
        $this->tagPrefix = $prefix;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $id
     * @return mixed
     */
    public function get($id)
    {
        $id = $this->prepareId($id);
        return $this->getAdapter()->get($id);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $id
     * @param mixed $data
     * @param int $ttl
     * @return bool
     */
    public function add($id, $data, $ttl = Cache::TTL_NO_EXPIRY)
    {
        $id = $this->prepareId($id);
        return $this->getAdapter()->add($id, $data, $ttl);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $id
     * @param mixed $data
     * @param int $ttl
     * @return bool
     */
    public function set($id, $data, $ttl = Cache::TTL_NO_EXPIRY)
    {
        $id = $this->prepareId($id);
        return $this->getAdapter()->set($id, $data, $ttl);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $id
     * @return bool
     */
    public function contains($id)
    {
        $id = $this->prepareId($id);
        return $this->getAdapter()->contains($id);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $id
     * @return mixed
     */
    public function delete($id)
    {
        $id = $this->prepareId($id);
        return $this->getAdapter()->delete($id);
    }

    /**
     * {@inheritdoc}
     *
     * @return mixed
     */
    public function flush()
    {
        return $this->getAdapter()->flush();
    }

    /**
     * Get underlying cache adapter
     * @return Adapter\AbstractAdapter
     */
    public function getAdapter()
    {
        if (!$this->adapter) {
            $this->adapter = $this->initAdapter($this->getOption('adapter'));
        }

        return $this->adapter;
    }

    /**
     * Get underlying tag adapter
     * @throws CacheException
     * @throws ConfigException
     * @return Adapter\AbstractAdapter
     */
    public function getTagAdapter()
    {
        if (!$this->tagAdapter) {
            // create instance of new adapter
            if ($tagAdapter = $this->getOption('tagAdapter')) {
                $this->tagAdapter = $this->initAdapter($tagAdapter);
            } elseif ($adapter = $this->getAdapter()) {
                $this->tagAdapter = $adapter;
            } else {
                throw new ConfigException("Tag Adapter can't initialize. Configuration is missed");
            }
        }
        return $this->tagAdapter;
    }

    /**
     * Init adapter
     *
     * @param string $adapterName
     * @throws CacheException
     * @return Adapter\AbstractAdapter
     */
    protected function initAdapter($adapterName)
    {
        if (!$adapterSettings = $this->getOption('settings', $adapterName)) {
            throw new CacheException("Cache Adapter can't initialize. Configuration is missed");
        }

        $adapterName = ucfirst($adapterName);
        $adapterClass = '\\Bluz\\Cache\\Adapter\\' . $adapterName;

        $adapter = new $adapterClass($adapterSettings);
        return $adapter;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $id
     * @param string $tag
     * @return bool
     * @throws CacheException
     */
    public function addTag($id, $tag)
    {
        $tag = $this->tagPrefix . $tag;

        if ($this->getTagAdapter()->contains($tag)) {
            $identifiers = $this->getTagAdapter()->get($tag);
        } else {
            $identifiers = array();
        }

        // array may contain not unique values, but I can't see problem here
        $identifiers[] = $this->prepareId($id);

        return $this->getTagAdapter()->set($tag, $identifiers);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $tag
     * @return bool
     * @throws CacheException
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

        $this->tagAdapter->delete($tag);

        return true;
    }
}
