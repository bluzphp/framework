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
 * Class AbstractRowEntity
 * @package Bluz\Auth
 */
abstract class AbstractRowEntity extends Row implements EntityInterface
{
    /**
     * Get roles
     *
     * @return array
     */
    abstract public function getPrivileges();

    /**
     * Can entity login
     *
     * @throws AuthException
     * @return boolean
     */
    abstract public function tryLogin();

    /**
     * Has role a privilege
     *
     * @param string $module
     * @param string $privilege
     * @return boolean
     */
    public function hasPrivilege($module, $privilege)
    {
        $privileges = $this->getPrivileges();

        return in_array($module.':'.$privilege, $privileges);
    }

    /**
     * Login
     * @throw AuthException
     */
    public function login()
    {
        $this->tryLogin();
        app()->getAuth()->setIdentity($this);
    }
}
