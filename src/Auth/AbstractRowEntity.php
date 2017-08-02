<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Auth;

use Bluz\Db\Row;

/**
 * Abstract class for Users\Row
 *
 * @package Bluz\Auth
 */
abstract class AbstractRowEntity extends Row implements EntityInterface
{
    /**
     * Get user privileges
     *
     * @return array
     */
    abstract public function getPrivileges(): array;

    /**
     * Has role a privilege
     *
     * @param  string $module
     * @param  string $privilege
     *
     * @return bool
     */
    public function hasPrivilege($module, $privilege): bool
    {
        $privileges = $this->getPrivileges();

        return in_array("$module:$privilege", $privileges, true);
    }
}
