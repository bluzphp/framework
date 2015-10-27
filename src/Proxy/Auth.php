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

use Bluz\Auth\Auth as Instance;
use Bluz\Auth\EntityInterface;

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
 * @method   static void setIdentity(EntityInterface $identity)
 * @see      Bluz\Auth\Auth::setIdentity()
 *
 * @method   static EntityInterface getIdentity()
 * @see      Bluz\Auth\Auth::getIdentity()
 *
 * @method   static void clearIdentity()
 * @see      Bluz\Auth\Auth::clearIdentity()
 */
class Auth extends AbstractProxy
{
    /**
     * Init instance
     *
     * @return Instance
     */
    protected static function initInstance()
    {
        $instance = new Instance();
        $instance->setOptions(Config::getData('auth'));
        return $instance;
    }
}
