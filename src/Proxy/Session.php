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

use Bluz\Session\Session as Instance;

/**
 * Proxy to Session
 *
 * @package  Bluz\Proxy
 *
 * @method   static Instance getInstance()
 *
 * @method   static void  start()
 * @method   static void  destroy()
 * @method   static void  set($key, $value)
 * @method   static mixed get($key)
 * @method   static bool  contains($key)
 * @method   static void  delete($key)
 * @method   static string getId()
 * @method   static bool  regenerateId($deleteOldSession = true)
 * @method   static void  setSessionCookieLifetime($ttl)
 * @method   static void  expireSessionCookie()
 *
 * @author   Anton Shevchuk
 * @created  29.09.2014 12:15
 */
class Session extends AbstractProxy
{
    /**
     * Init instance
     *
     * @return Instance
     */
    protected static function initInstance()
    {
        $instance = new Instance();
        $instance->setOptions(Config::getData('session'));
        return $instance;
    }
}
