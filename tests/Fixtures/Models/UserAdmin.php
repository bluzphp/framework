<?php

/**
 * @namespace
 */

namespace Bluz\Tests\Fixtures\Models;

use Bluz\Auth\AbstractIdentity;
use Bluz\Proxy\Auth;

/**
 * Row
 *
 * @package  Bluz\Tests\Fixtures\Models
 *
 * @author   Anton Shevchuk
 * @created  28.03.14 18:25
 */
class UserAdmin extends AbstractIdentity
{
    /**
     * Get user privileges
     *
     * @return array
     */
    public function getPrivileges(): array
    {
        return [];
    }

    /**
     * Has role a privilege
     *
     * @param string $module
     * @param string $privilege
     *
     * @return boolean
     */
    public function hasPrivilege(string $module, string $privilege): bool
    {
        return true;
    }
}
