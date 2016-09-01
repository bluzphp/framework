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
 * @author   Anton Shevchuk
 *
 * @method   static Instance getInstance()
 *
 * @method   static bool add($id, $data, $ttl = Instance::TTL_NO_EXPIRY)
 * @see      Instance::add()
 *
 * @method   static bool set($id, $data, $ttl = Instance::TTL_NO_EXPIRY)
 * @see      Instance::set()
 *
 * @method   static mixed get($id)
 * @see      Instance::get()
 *
 * @method   static bool contains($id)
 * @see      Instance::contains()
 *
 * @method   static mixed delete($id)
 * @see      Instance::delete()
 *
 * @method   static mixed flush()
 * @see      Instance::flush()
 *
 * @method   static bool addTag($id, $tag)
 * @see      Instance::addTag()
 *
 * @method   static bool deleteByTag($tag)
 * @see      Instance::deleteByTag()
 */
class Cache
{
    use ProxyTrait;

    /**
     * No expiry TTL
     */
    const TTL_NO_EXPIRY = Instance::TTL_NO_EXPIRY;

    /**
     * Init instance
     *
     * @return Instance|Nil
     */
    protected static function initInstance()
    {
        $config = Config::getData('cache');

        if ($config && isset($config['enabled']) && $config['enabled']) {
            $instance = new Instance();
            $instance->setOptions($config);
            return $instance;
        } else {
            return new Nil();
        }
    }
}
