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
 * Bluz cache driver interface
 * @author Murzik
 */
interface CacheInterface
{
    /**
     * Retrieve data from cache by identifier
     * @param string $id cache entry identifier
     * @return mixed
     */
    public function get($id);

    /**
     * Put data into cache.
     * Overwrite cache entry with given id if it exists.
     * @param string $id cache entry identifier
     * @param mixed $data data to cache
     * @param int $ttl Time To Live in seconds 0 == infinity
     * @return boolean
     */
    public function set($id, $data, $ttl = Cache::TTL_NO_EXPIRY);

    /**
     * Put data into cache.
     * Operation will fail if cache entry with given id already exists
     * @param string $id cache entry identifier
     * @param mixed $data data to cache
     * @param int $ttl Time To Live in seconds 0 == infinity
     * @return boolean
     */
    public function add($id, $data, $ttl = Cache::TTL_NO_EXPIRY);

    /**
     * Test for cache entry existence
     * @param string $id cache entry identifier
     * @return boolean
     */
    public function contains($id);

    /**
     * Delete cache entry
     * @param string $id
     * @return mixed
     */
    public function delete($id);

    /**
     * Invalidate(delete) all cache entries
     * @return mixed
     */
    public function flush();
}
