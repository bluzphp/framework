<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Auth;

/**
 *
 */
interface EntityInterface
{
    /**
     * Get user privileges
     *
     * @return array
     */
    public function getPrivileges();

    /**
     * Has role a privilege
     *
     * @param string $module
     * @param string $privilege
     * @return boolean
     */
    public function hasPrivilege($module, $privilege);
}
