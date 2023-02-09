<?php

/**
 * @namespace
 */

namespace Bluz\Tests\Fixtures\Models;

use Bluz\Auth\AbstractIdentity;

/**
 * Row
 *
 * @package  Bluz\Tests\Fixtures\Models
 *
 * @author   Anton Shevchuk
 */
class UserMember extends AbstractIdentity
{
    protected array $privileges = [];

    /**
     * @param array $privileges
     */
    public function setPrivileges(array $privileges): void
    {
        $this->privileges = $privileges;
    }

    /**
     * Get user privileges
     *
     * @return array
     */
    public function getPrivileges(): array
    {
        return $this->privileges;
    }
}
