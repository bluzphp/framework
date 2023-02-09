<?php

/**
 * @namespace
 */

namespace Bluz\Tests\Fixtures\Models;

use Bluz\Application\ApplicationException;
use Bluz\Auth\AbstractIdentity;

/**
 * Row
 *
 * @package  Bluz\Tests\Fixtures\Models
 *
 * @author   Anton Shevchuk
 * @created  28.03.14 18:25
 */
class UserGuest extends AbstractIdentity
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
        return false;
    }
}
