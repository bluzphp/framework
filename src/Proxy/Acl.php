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

use Bluz\Acl\Acl as Instance;

/**
 * Proxy to Acl
 *
 * Example of usage
 *     use Bluz\Proxy\Acl;
 *
 *     if (!Acl::isAllowed('users', 'profile')) {
 *          throw new Exception('You do not have permission to access user profiles');
 *     }
 *
 * @package  Bluz\Proxy
 *
 * @method   static Instance getInstance()
 *
 * @method   static bool isAllowed($module, $privilege)
 * @see      Bluz\Acl\Acl::isAllowed()
 *
 * @author   Anton Shevchuk
 * @created  26.09.2014 18:02
 */
class Acl extends AbstractProxy
{
    /**
     * Init instance
     *
     * @return Instance
     */
    protected static function initInstance()
    {
        return new Instance();
    }
}
