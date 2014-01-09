<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Cache\Adapter;

use Bluz\Cache\Cache;
use Bluz\Cache\CacheException;

/**
 *
 */
class Memcached extends AbstractAdapter
{
    /**
     * @var \Memcached
     */
    protected $memcached = null;

    /**
     * Check and setup memcached servers
     * @param array $settings
     * @throws \Bluz\Cache\CacheException
     */
    public function __construct($settings = array())
    {
        // check extension
        if (!extension_loaded('memcached')) {
            throw new CacheException(
                "Memcached extension not installed/enabled.
                Install and/or enable memcached extension. See phpinfo() for more information"
            );
        }

        // check settings
        if (!is_array($settings) or empty($settings) or !isset($settings['servers'])) {
            throw new CacheException(
                "Memcached configuration is missed.
                Please check 'cache' configuration section"
            );
        }

        parent::__construct($settings);
    }

    /**
     * getHandler
     *
     * @return \Memcached
     */
    public function getHandler()
    {
        if (!$this->memcached) {
            $this->memcached = new \Memcached();
            $this->memcached->addServers($this->settings['servers']);
            if (isset($this->settings['options'])) {
                foreach ($this->settings['options'] as $key => $value) {
                    $this->memcached->setOption($key, $value);
                }
            }
        }
        return $this->memcached;
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
    protected function doAdd($id, $data, $ttl = Cache::TTL_NO_EXPIRY)
    {
        return $this->getHandler()->add($id, $data, $ttl);
    }

    /**
     * {@inheritdoc}
     */
    protected function doSet($id, $data, $ttl = Cache::TTL_NO_EXPIRY)
    {
        return $this->getHandler()->set($id, $data, $ttl);
    }

    /**
     * {@inheritdoc}
     */
    protected function doContains($id)
    {
        $this->getHandler()->get($id);
        return $this->getHandler()->getResultCode() !== \Memcached::RES_NOTFOUND;
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
        return $this->getHandler()->flush();
    }
}
