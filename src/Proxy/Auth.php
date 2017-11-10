<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Proxy;

use Bluz\Auth\Auth as Instance;
use Bluz\Auth\IdentityInterface;

/**
 * Proxy to Auth
 *
 * Example of usage
 *     use Bluz\Proxy\Auth;
 *
 *     $user = Auth::getIdentity();
 *
 * @package  Bluz\Proxy
 * @author   Anton Shevchuk
 *
 * @method   static Instance getInstance()
 *
 * @method   static void setIdentity(IdentityInterface $identity)
 * @see      Instance::setIdentity()
 *
 * @method   static IdentityInterface getIdentity()
 * @see      Instance::getIdentity()
 *
 * @method   static void clearIdentity()
 * @see      Instance::clearIdentity()
 */
final class Auth
{
    use ProxyTrait;

    /**
     * Init instance
     *
     * @return Instance
     */
    private static function initInstance()
    {
        $instance = new Instance();
        $instance->setOptions(Config::getData('auth'));
        return $instance;
    }
}
