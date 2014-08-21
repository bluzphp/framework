<?php
/**
 * @namespace
 */
namespace Bluz\Tests\Fixtures\Models;

use Bluz\Application\Exception\ApplicationException;
use Bluz\Auth\AbstractRowEntity;

/**
 * Row
 *
 * @package  Bluz\Tests\Fixtures\Models
 *
 * @author   Anton Shevchuk
 * @created  28.03.14 18:25
 */
class UserGuest extends AbstractRowEntity
{
    /**
     * Can entity login
     *
     * @throws ApplicationException
     */
    public function login()
    {
        throw new ApplicationException("User status is undefined in system");
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
        return false;
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
        return false;
    }
}
