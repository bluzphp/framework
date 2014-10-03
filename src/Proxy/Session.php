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
 * @see      Session::start()
 * @method   static void  destroy()
 * @see      Session::destroy()
 * @method   static void  set($key, $value)
 * @see      Session::set()
 * @method   static mixed get($key)
 * @see      Session::get()
 * @method   static bool  contains($key)
 * @see      Session::contains()
 * @method   static void  delete($key)
 * @see      Session::delete()
 * @method   static string getId()
 * @see      Session::getId()
 * @method   static bool  regenerateId($deleteOldSession = true)
 * @see      Session::regenerateId()
 * @method   static void  setSessionCookieLifetime($ttl)
 * @see      Session::setSessionCookieLifetime()
 *
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
