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
 * Example of usage
 * <code>
 *     use Bluz\Proxy\Session;
 *
 *     // lazy session loading
 *     Session::set('some key in session', 'value example');
 *     Session::get('some key in session');
 * </code>
 *
 * @package  Bluz\Proxy
 * @author   Anton Shevchuk
 *
 * @method   static Instance getInstance()
 *
 * @method   static void  start()
 * @see      Bluz\Session\Session::start()
 * @method   static void  destroy()
 * @see      Bluz\Session\Session::destroy()
 * @method   static void  set($key, $value)
 * @see      Bluz\Session\Session::set()
 * @method   static mixed get($key)
 * @see      Bluz\Session\Session::get()
 * @method   static bool  contains($key)
 * @see      Bluz\Session\Session::contains()
 * @method   static void  delete($key)
 * @see      Bluz\Session\Session::delete()
 * @method   static string getId()
 * @see      Bluz\Session\Session::getId()
 * @method   static bool  regenerateId($deleteOldSession = true)
 * @see      Bluz\Session\Session::regenerateId()
 * @method   static void  setSessionCookieLifetime($ttl)
 * @see      Bluz\Session\Session::setSessionCookieLifetime()
 *
 * @method   static void  expireSessionCookie()
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
