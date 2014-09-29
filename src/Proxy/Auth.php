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
 * @package  Bluz\Proxy
 *
 * @method   static Instance getInstance()
 *
 * @method   static void setIdentity(EntityInterface $identity)
 * @method   static EntityInterface getIdentity()
 * @method   static void clearIdentity()
 *
 * @author   Anton Shevchuk
 * @created  26.09.2014 18:02
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
