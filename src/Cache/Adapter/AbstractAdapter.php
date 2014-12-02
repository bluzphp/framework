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
namespace Bluz\Cache\Adapter;

use Bluz\Cache\Cache;
use Bluz\Cache\CacheException;
use Bluz\Cache\CacheInterface;

/**
 * Base class for all cache adapters within Bluz\Cache package
 *
 * @package Bluz\Cache\Adapter
 * @author  murzik
 */
abstract class AbstractAdapter implements CacheInterface
{
    /**
     * Cache settings
     * @var array
     */
    protected $settings = array();

    /**
     * Setup adapter settings
     *
     * @param array $settings setup adapter
     */
    public function __construct($settings = array())
    {
        $this->settings = array_replace_recursive($this->settings, $settings);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $id
     * @return mixed
     * @throws CacheException
     */
    public function get($id)
    {
        return $this->doGet($id);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $id
     * @return bool
     * @throws CacheException
     */
    public function contains($id)
    {
        return $this->doContains($id);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $id
     * @param mixed $data
     * @param int $ttl
     * @return bool
     * @throws CacheException
     */
    public function add($id, $data, $ttl = Cache::TTL_NO_EXPIRY)
    {
        return $this->doAdd($id, $data, $ttl);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $id
     * @param mixed $data
     * @param int $ttl
     * @return bool
     * @throws CacheException
     */
    public function set($id, $data, $ttl = Cache::TTL_NO_EXPIRY)
    {
        return $this->doSet($id, $data, $ttl);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $id
     * @return mixed
     * @throws CacheException
     */
    public function delete($id)
    {
        return $this->doDelete($id);
    }

    /**
     * {@inheritdoc}
     *
     * @return mixed
     */
    public function flush()
    {
        return $this->doFlush();
    }

    /**
     * Must be implemented in particular cache driver implementation
     * Actual  work for \Bluz\Cache\CacheInterface::flush() goes here
     * @see \Bluz\Cache\CacheInterface::flush()
     * @return mixed
     */
    abstract protected function doFlush();

    /**
     * Must be implemented in particular cache driver implementation
     * Actual  work for \Bluz\Cache\CacheInterface::get() goes here
     * @see \Bluz\Cache\CacheInterface::get()
     * @param string $id
     * @return mixed
     */
    abstract protected function doGet($id);

    /**
     * Must be implemented in particular cache driver implementation
     * Actual  work for \Bluz\Cache\CacheInterface::add() goes here
     * @see \Bluz\Cache\CacheInterface::add()
     * @param string $id
     * @param mixed $data
     * @param int $ttl
     * @return mixed
     */
    abstract protected function doAdd($id, $data, $ttl = Cache::TTL_NO_EXPIRY);

    /**
     * Must be implemented in particular cache driver implementation
     * Actual  work for \Bluz\Cache\CacheInterface::set() goes here
     * @see \Bluz\Cache\CacheInterface::set()
     * @param string $id
     * @param mixed $data
     * @param int $ttl
     * @return mixed
     */
    abstract protected function doSet($id, $data, $ttl = Cache::TTL_NO_EXPIRY);

    /**
     * Must be implemented in particular cache driver implementation
     * Actual  work for \Bluz\Cache\CacheInterface::delete() goes here
     * @see \Bluz\Cache\CacheInterface::delete()
     * @param string $id
     * @return mixed
     */
    abstract protected function doDelete($id);

    /**
     * Must be implemented in particular cache driver implementation
     * Actual  work for \Bluz\Cache\CacheInterface::contains() goes here
     * @see Bluz\Cache\CacheInterface::contains()
     * @param string $id
     * @return mixed
     */
    abstract protected function doContains($id);
}
