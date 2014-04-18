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

use Bluz\Cache\InvalidArgumentException;
use Bluz\Cache\Cache;
use Bluz\Cache\CacheInterface;

/**
 * Base class for all cache adapters within Bluz\Cache package
 *
 * @package Bluz\Cache\Adapter
 * @author murzik
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
     * @throws \Bluz\Cache\InvalidArgumentException
     */
    public function get($id)
    {
        $id = $this->castToString($id);
        return $this->doGet($id);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $id
     * @return bool
     * @throws \Bluz\Cache\InvalidArgumentException
     */
    public function contains($id)
    {
        $id = $this->castToString($id);
        return $this->doContains($id);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $id
     * @param mixed $data
     * @param int $ttl
     * @return bool
     * @throws \Bluz\Cache\InvalidArgumentException
     */
    public function add($id, $data, $ttl = Cache::TTL_NO_EXPIRY)
    {
        $id = $this->castToString($id);
        return $this->doAdd($id, $data, $ttl);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $id
     * @param mixed $data
     * @param int $ttl
     * @return bool
     * @throws \Bluz\Cache\InvalidArgumentException
     */
    public function set($id, $data, $ttl = Cache::TTL_NO_EXPIRY)
    {
        $id = $this->castToString($id);
        return $this->doSet($id, $data, $ttl);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $id
     * @return mixed
     * @throws \Bluz\Cache\InvalidArgumentException
     */
    public function delete($id)
    {
        $id = $this->castToString($id);
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
     * Cast given $inputValue to string.
     * @param mixed $inputValue
     * @throws InvalidArgumentException if given $input value not a number or string
     * @return string $castedToString
     * @internal defence from "fool".
     *           Attempt to cast to string object will lead to cache entry with id "Object".
     *           Which is wrong.
     */
    protected function castToString($inputValue)
    {
        if (!is_string($inputValue) && !is_int($inputValue)) {
            $msg = "<String> or <Integer> expected. But "
                . "<" . gettype($inputValue) . "> given.";
            throw new InvalidArgumentException($msg);
        }

        return (string)$inputValue;
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
