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
namespace Bluz\Acl;

use Bluz\Common\Options;
use Bluz\Proxy\Auth;

/**
 * Acl
 *
 * @package  Bluz\Acl
 * @author   Anton Shevchuk
 * @link     https://github.com/bluzphp/framework/wiki/Acl
 */
class Acl
{
    use Options;

    /**
     * Check user access by pair module-privilege
     *
     * @param  string $module
     * @param  string $privilege
     *
     * @return bool
     */
    public function isAllowed($module, $privilege) : bool
    {
        if ($privilege) {
            $user = Auth::getIdentity();
            if (!$user || !$user->hasPrivilege($module, $privilege)) {
                return false;
            }
        }
        return true;
    }
}
