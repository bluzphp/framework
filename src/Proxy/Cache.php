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
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface as Instance;
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
     * @param string $adapter
     *
     * @return Instance|false
     * @throws ComponentException
     */
    public static function getAdapter(string $adapter)
    {
        $config = Config::get('cache');

        if ($config && $adapter && isset($config['enabled']) && $config['enabled']) {
            if (!isset($config['pools'][$adapter])) {
                throw new ComponentException("Class `Proxy\\Cache` required configuration for `$adapter` adapter");
            }
            if (!isset(Cache::$pools[$adapter])) {
                Cache::$pools[$adapter] = $config['pools'][$adapter]();
            }
            return Cache::$pools[$adapter];
        }
        return false;
    }

    /**
     * Get value of cache item
     *
     * @param string $key
     *
     * @return mixed
     */
    public static function get(string $key)
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
    public static function set(string $key, $data, int $ttl = self::TTL_NO_EXPIRY, array $tags = [])
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
    public static function prepare(string $key): string
    {
        return str_replace(['-', '/', '\\', '@', ':'], '_', $key);
    }

    /**
     * Clear cache items by tag
     *
     * @param  string $tag
     *
     * @return bool
     * @throws InvalidArgumentException
     */
    public static function clearTag(string $tag): bool
    {
        if (self::getInstance() instanceof TagAwareAdapterInterface) {
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
     */
    public static function clearTags(array $tags): bool
    {
        if (self::getInstance() instanceof TagAwareAdapterInterface) {
            return self::getInstance()->invalidateTags($tags);
        }
        return false;
    }
}
