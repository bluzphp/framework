<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Auth;

/**
 * Interface EntityInterface
 *
 * @package Bluz\Auth
 *
 * @property integer $id
 * @property string  $login
 * @property string  $email
 */
interface EntityInterface
{
    /**
     * Get user privileges
     *
     * @return array
     */
    public function getPrivileges() : array;

    /**
     * Has role a privilege
     *
     * @param  string $module
     * @param  string $privilege
     *
     * @return bool
     */
    public function hasPrivilege($module, $privilege) : bool;
}
