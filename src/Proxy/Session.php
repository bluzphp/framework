<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

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
 * @see      Instance::start()
 * @method   static void  destroy()
 * @see      Instance::destroy()
 * @method   static void  set($key, $value)
 * @see      Instance::set()
 * @method   static mixed get($key)
 * @see      Instance::get()
 * @method   static bool  contains($key)
 * @see      Instance::contains()
 * @method   static void  delete($key)
 * @see      Instance::delete()
 * @method   static string getId()
 * @see      Instance::getId()
 * @method   static bool  regenerateId($deleteOldSession = true)
 * @see      Instance::regenerateId()
 * @method   static void  setSessionCookieLifetime($ttl)
 * @see      Instance::setSessionCookieLifetime()
 *
 * @method   static void  expireSessionCookie()
 */
final class Session
{
    use ProxyTrait;

    /**
     * Init instance
     *
     * @return Instance
     */
    private static function initInstance() : Instance
    {
        $instance = new Instance();
        $instance->setOptions(Config::get('session'));
        return $instance;
    }
}
