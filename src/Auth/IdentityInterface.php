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
 */
interface IdentityInterface
{
    /**
     * Get an ID that can uniquely identify a user
     *
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * Get user's privileges
     *
     * @return array
     */
    public function getPrivileges(): array;

    /**
     * Has it privilege?
     *
     * @param string $module
     * @param string $privilege
     *
     * @return bool
     */
    public function hasPrivilege(string $module, string $privilege): bool;
}
