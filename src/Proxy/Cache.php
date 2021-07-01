<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Proxy;

use Bluz\Common\Exception\ComponentException;
use Cache\Hierarchy\HierarchicalPoolInterface;
use Cache\TagInterop\TaggableCacheItemPoolInterface as Instance;
use Psr\Cache\InvalidArgumentException;

/**
 * Proxy to Cache
 *
 * Example of usage
 *     use Bluz\Proxy\Cache;
 *
 *     if (!$result = Cache::get('some unique id')) {
 *          $result = 2*2;
 *          Cache::set('some unique id', $result);
 *     }
 *
 * @package  Bluz\Proxy
 * @author   Anton Shevchuk
 *
 * @method   static Instance|false getInstance()
 *
 * @method   static bool delete($key)
 * @see      Instance::deleteItem()
 *
 * @method   static bool clear()
 * @see      Instance::clear()
 */
final class Cache
{
    use ProxyTrait;

    /**
     * No expiry TTL value
     */
    public const TTL_NO_EXPIRY = 0;

    /**
     * @var array
     */
    private static $pools = [];

    /**
     * Init cache instance
     *
     * @return Instance|false
     * @throws ComponentException
     */
    private static function initInstance()
    {
        $adapter = Config::get('cache', 'adapter');
        return self::getAdapter($adapter);
    }

    /**
     * Get Cache Adapter
     *
     * @param  string $adapter
     *
     * @return Instance|false
     * @throws ComponentException
     */
    public static function getAdapter($adapter)
    {
        $config = Config::get('cache');

        if ($config && $adapter && isset($config['enabled']) && $config['enabled']) {
            if (!isset($config['pools'][$adapter])) {
                throw new ComponentException("Class `Proxy\\Cache` required configuration for `$adapter` adapter");
            }
            if (!isset(static::$pools[$adapter])) {
                static::$pools[$adapter] = $config['pools'][$adapter]();
            }
            return static::$pools[$adapter];
        }
        return false;
    }

    /**
     * Get value of cache item
     *
     * @param  string $key
     *
     * @return mixed
     */
    public static function get($key)
    {
        if (!$cache = self::getInstance()) {
            return false;
        }

        $key = self::prepare($key);

        try {
            if ($cache->hasItem($key)) {
                $item = $cache->getItem($key);
                if ($item->isHit()) {
                    return $item->get();
                }
            }
        } catch (InvalidArgumentException $e) {
            // something going wrong
            Logger::error($e->getMessage());
        }

        return false;
    }

    /**
     * Set value of cache item
     *
     * @param  string   $key
     * @param  mixed    $data
     * @param  int      $ttl
     * @param  string[] $tags
     *
     * @return bool
     */
    public static function set($key, $data, $ttl = self::TTL_NO_EXPIRY, $tags = [])
    {
        if (!$cache = self::getInstance()) {
            return false;
        }

        $key = self::prepare($key);
        try {
            $item = $cache->getItem($key);
            $item->set($data);

            if (self::TTL_NO_EXPIRY !== $ttl) {
                $item->expiresAfter($ttl);
            }

            if (!empty($tags)) {
                $item->setTags($tags);
            }

            return $cache->save($item);
        } catch (InvalidArgumentException $e) {
            // something going wrong
            Logger::error($e->getMessage());
        }

        return false;
    }

    /**
     * Prepare key
     *
     * @param  string $key
     *
     * @return string
     */
    public static function prepare($key): string
    {
        return str_replace(['-', '/', '\\', '@', ':'], '_', $key);
    }

    /**
     * Clear cache items by tag
     *
     * @param string $tag
     *
     * @return bool
     * @throws InvalidArgumentException
     * @see    TaggableCacheItemPoolInterface::invalidateTag()
     *
     */
    public static function clearTag($tag): bool
    {
        if (self::getInstance() instanceof HierarchicalPoolInterface) {
            return self::getInstance()->invalidateTag($tag);
        }
        return false;
    }

    /**
     * Clear cache items by tags
     *
     * @param array $tags
     *
     * @return bool
     * @throws InvalidArgumentException
     * @see    TaggableCacheItemPoolInterface::invalidateTags()
     *
     */
    public static function clearTags(array $tags): bool
    {
        if (self::getInstance() instanceof HierarchicalPoolInterface) {
            return self::getInstance()->invalidateTags($tags);
        }
        return false;
    }
}
