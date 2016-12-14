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
namespace Bluz\Proxy;

use Bluz\Common\Exception\ComponentException;
use Bluz\Common\Singleton;
use Cache\Adapter\Common\CacheItem as Item;
use Psr\Cache\CacheItemPoolInterface as Instance;

/**
 * Proxy to Cache
 *
 * Example of usage
 *     use Bluz\Proxy\Cache;
 *
 *     if (!Cache::hasItem('some unique id')) {
 *          $result = 2*2;
 *          $item = Cache::getItem('some unique id');
 *          $item->set($result);
 *          Cache::save($item);
 *     }
 *
 * @package  Bluz\Proxy
 * @author   Anton Shevchuk
 *
 * @method   static Instance getInstance()
 *
 * @method   static Item getItem($key)
 * @see      Instance::getItem()
 *
 * @method   static array|\Traversable getItems(array $keys = array())
 * @see      Instance::getItems()
 *
 * @method   static bool hasItem($key)
 * @see      Instance::hasItem()
 *
 * @method   static bool deleteItem($key)
 * @see      Instance::deleteItem()
 *
 * @method   static bool deleteItems(array $keys)
 * @see      Instance::deleteItems()
 *
 * @method   static bool save(Item $item)
 * @see      Instance::save()
 *
 * @method   static bool clear()
 * @see      Instance::clear()
 *
 * @method   static bool clearTags(array $tags)
 * @see      TaggablePoolInterface::clearTags()
 */
class Cache
{
    use Singleton;

    const TTL_NO_EXPIRY = 0;

    /**
     * @var array
     */
    static $pools = [];

    /**
     * Handle dynamic, static calls to the object.
     *
     * @param  string $method
     * @param  array $args
     * @return mixed
     * @throws ComponentException
     */
    public static function __callStatic($method, $args)
    {
        if (false === static::getInstance()) {
            throw new ComponentException(
                "Class `Proxy\\Cache` is disabled, please use safe-methods.\n".
                "For more information read documentation at https://github.com/bluzphp/framework/wiki/Cache"
            );
        }

        return static::getInstance()->$method(...$args);
    }


    /**
     * Init cache instance
     *
     * @return Instance|false
     * @throws ComponentException
     */
    protected static function initInstance()
    {
        $adapter = Config::getData('cache', 'adapter');
        return self::getAdapter($adapter);
    }

    /**
     * Get Cache Adapter
     *
     * @param string $adapter
     * @return Instance|false
     * @throws ComponentException
     */
    public static function getAdapter($adapter)
    {
        $config = Config::getData('cache');

        if ($config && $adapter && isset($config['enabled']) && $config['enabled']) {
            if (!isset($config['pools'][$adapter])) {
                throw new ComponentException("Class `Proxy\\Cache` required configuration for `$adapter` adapter");
            } else {
                if (!isset(static::$pools[$adapter])) {
                    static::$pools[$adapter] = $config['pools'][$adapter]();
                }
                return static::$pools[$adapter];
            }
        }
        return false;
    }

    /**
     * Get value of cache item
     *
     * @param string $key
     * @return mixed
     */
    public static function get($key)
    {
        if (!self::getInstance()) {
            return false;
        }

        if (self::hasItem($key)) {
            $item = self::getItem($key);
            if ($item->isHit()) {
                return $item->get();
            }
        }
        return false;
    }

    /**
     * Set value of cache item
     *
     * @param string $key
     * @param mixed $data
     * @param int $ttl
     * @param array $tags
     * @return bool
     */
    public static function set($key, $data, $ttl = self::TTL_NO_EXPIRY, $tags = [])
    {
        if (!self::getInstance()) {
            return false;
        }

        $item = self::getItem($key);
        $item->set($data);

        if (self::TTL_NO_EXPIRY !== $ttl) {
            $item->expiresAfter($ttl);
        }

        if (!empty($tags)) {
            $item->setTags($tags);
        }

        return self::save($item);
    }

    /**
     * Delete cache item
     *
     * @param string $key
     * @return bool
     */
    public static function delete($key)
    {
        if (!self::getInstance()) {
            return false;
        }

        return self::deleteItem($key);
    }

    /**
     * Prepare key
     *
     * @return string
     */
    public static function prepare($key)
    {
        return str_replace(['-', '/'], '_', $key);
    }
}
