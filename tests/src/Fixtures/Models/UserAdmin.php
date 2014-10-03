<?php
/**
 * @namespace
 */
namespace Bluz\Tests\Fixtures\Models;

use Bluz\Auth\AbstractRowEntity;
use Bluz\Proxy\Auth;

/**
 * Row
 *
 * @package  Bluz\Tests\Fixtures\Models
 *
 * @author   Anton Shevchuk
 * @created  28.03.14 18:25
 */
class UserAdmin extends AbstractRowEntity
{
    /**
     * Can entity login
     *
     * @return void
     */
    public function login()
    {
        Auth::setIdentity($this);
    }

    /**
     * Get user privileges
     *
     * @return array
     */
    public function getPrivileges()
    {
        return array();
    }

    /**
     * Check user role
     *
     * @param integer $roleId
     * @return boolean
     */
    public function hasRole($roleId)
    {
        return true;
    }

    /**
     * Has role a privilege
     *
     * @param string $module
     * @param string $privilege
     * @return boolean
     */
    public function hasPrivilege($module, $privilege)
    {
        return true;
    }
}
