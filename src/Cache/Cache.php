<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
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
 *     - 'cacheAdapter' => Settings for setup Instance of Bluz\Cache\CacheInterface.
 *                    Required if option 'enabled' set to true
 *     - 'tagAdapter' => Settings fir setup Instance of Bluz\Cache\CacheInterface. Optional.
 *                    If it is not set, 'cacheAdapter' instance will be used as a tag adapter
 *
 * @author murzik
 */
use Bluz\Cache\Adapter;
use Bluz\Common\Options;
use Bluz\Config\ConfigException;

class Cache implements CacheInterface, TagableInterface
{
    use Options;

    /**
     * No expiry TTL
     */
    const TTL_NO_EXPIRY = 0;

    /**
     * @var Adapter\AbstractAdapter
     */
    protected $cacheAdapter = null;

    /**
     * @var Adapter\AbstractAdapter
     */
    protected $tagAdapter = null;

    /**
     * @var string
     */
    protected $tagPrefix = '@:';

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
                "Missed `cacheAdapter` option in `cache` configuration. <br/>\n" .
                "Read more: <a href='https://github.com/bluzphp/framework/wiki/Cache'>".
                "https://github.com/bluzphp/framework/wiki/Cache</a>"
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
    public function add($id, $data, $ttl = Cache::TTL_NO_EXPIRY)
    {
        return $this->getAdapter()->add($id, $data, $ttl);
    }

    /**
     * {@inheritdoc}
     */
    public function set($id, $data, $ttl = Cache::TTL_NO_EXPIRY)
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

        $this->tagAdapter->delete($tag);

        return true;
    }
}
