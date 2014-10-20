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
namespace Bluz\Auth;

use Bluz\Db\Row;

/**
 * Abstract class for Users\Row
 *
 * @property integer $id
 * @package Bluz\Auth
 */
abstract class AbstractRowEntity extends Row implements EntityInterface
{
    /**
     * Can entity login
     * @throws AuthException
     * @return bool
     */
    abstract public function login();

    /**
     * Get privileges
     * @return array
     */
    abstract public function getPrivileges();

    /**
     * Has role a privilege
     * @param string $module
     * @param string $privilege
     * @return bool
     */
    public function hasPrivilege($module, $privilege)
    {
        $privileges = $this->getPrivileges();

        return in_array($module.':'.$privilege, $privileges);
    }
}
