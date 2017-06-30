<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

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
 * @author   Anton Shevchuk
 *
 * @method   static Instance getInstance()
 *
 * @method   static bool isAllowed($module, $privilege)
 * @see      Instance::isAllowed()
 */
class Acl
{
    use ProxyTrait;

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
