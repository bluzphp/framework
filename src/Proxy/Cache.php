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

use Bluz\Cache\Cache as Instance;
use Bluz\Common\Nil;

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
 *
 * @method   static Instance getInstance()
 *
 * @method   static bool add($id, $data, $ttl = Instance::TTL_NO_EXPIRY)
 * @see      Bluz\Cache\Cache::add()
 *
 * @method   static bool set($id, $data, $ttl = Instance::TTL_NO_EXPIRY)
 * @see      Bluz\Cache\Cache::set()
 *
 * @method   static mixed get($id)
 * @see      Bluz\Cache\Cache::get()
 *
 * @method   static bool contains($id)
 * @see      Bluz\Cache\Cache::contains()
 *
 * @method   static mixed delete($id)
 * @see      Bluz\Cache\Cache::delete()
 *
 * @method   static mixed flush()
 * @see      Bluz\Cache\Cache::flush()
 *
 * @method   static bool addTag($id, $tag)
 * @see      Bluz\Cache\Cache::addTag()
 *
 * @method   static bool deleteByTag($tag)
 * @see      Bluz\Cache\Cache::deleteByTag()
 *
 * @author   Anton Shevchuk
 * @created  26.09.2014 13:05
 */
class Cache extends AbstractProxy
{
    /**
     * No expiry TTL
     */
    const TTL_NO_EXPIRY = Instance::TTL_NO_EXPIRY;

    /**
     * Init instance
     *
     * @return Instance
     */
    protected static function initInstance()
    {
        $config = Config::getData('cache');
        if (!$config or !isset($config['enabled']) or !$config['enabled']) {
            return new Nil();
        } else {
            $instance = new Instance();
            $instance->setOptions($config);
            return $instance;
        }
    }
}
