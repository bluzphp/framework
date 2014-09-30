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
 * @package  Bluz\Proxy
 *
 * @method   static Instance getInstance()
 *
 * @method   static bool add($id, $data, $ttl = Instance::TTL_NO_EXPIRY)
 * @method   static bool set($id, $data, $ttl = Instance::TTL_NO_EXPIRY)
 * @method   static mixed get($id)
 * @method   static bool contains($id)
 * @method   static mixed delete($id)
 * @method   static mixed flush()
 * @method   static bool addTag($id, $tag)
 * @method   static bool deleteByTag($tag)
 *
 * @author   Anton Shevchuk
 * @created  26.09.2014 13:05
 */
class Cache extends AbstractProxy
{
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
