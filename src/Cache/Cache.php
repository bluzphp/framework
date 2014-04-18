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
 *     'settings' =>
 *         'cacheAdapter' => Settings for setup Instance of Bluz\Cache\CacheInterface.
 *                    Required if option 'enabled' set to true
 *         'tagAdapter' => Settings fir setup Instance of Bluz\Cache\CacheInterface. Optional.
 *                    If it is not set, 'cacheAdapter' instance will be used as a tag adapter
 *
 * @package Bluz\Cache
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
     * @var Adapter\AbstractAdapter
     */
    protected $cacheAdapter = null;

    /**
     * Instance of tag adapter
     * @var Adapter\AbstractAdapter
     */
    protected $tagAdapter = null;

    /**
     * Prefix for tags
     * @var string
     */
    protected $tagPrefix = '@:';

    /**
     * Check Cache configuration
     *
     * @throws \Bluz\Config\ConfigException
     * @return boolean
     */
    protected function checkOptions()
    {
        // check cache Adapter instance and settings for initialize it
        if (!isset($this->options['settings'], $this->options['settings']['cacheAdapter'])) {
            throw new ConfigException(
                "Missed `cacheAdapter` option in cache `settings` configuration. <br/>\n" .
                "Read more: <a href='https://github.com/bluzphp/framework/wiki/Cache'>".
                "https://github.com/bluzphp/framework/wiki/Cache</a>"
            );
        }
        return true;
    }

    /**
     * Setup cache adapter
     * @param CacheInterface $adapter
     */
    public function setCacheAdapter(CacheInterface $adapter)
    {
        $this->cacheAdapter = $adapter;
    }

    /**
     * Setup tag adapter
     * @param CacheInterface $adapter
     */
    public function setTagAdapter(CacheInterface $adapter)
    {
        $this->tagAdapter = $adapter;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $id
     * @return mixed
     */
    public function get($id)
    {
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
        if (!$this->cacheAdapter) {
            $this->cacheAdapter = $this->initAdapter($this->options['settings']['cacheAdapter']);
        }

        return $this->cacheAdapter;
    }

    /**
     * Get underlying tag adapter
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
     * Init adapter
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
        } elseif (isset($settings['name'], $settings['settings'])) {
            $adapterName = $settings['name'];
            $adapterSettings = $settings['settings'];
        } else {
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
