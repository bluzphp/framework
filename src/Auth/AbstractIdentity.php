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
 *
 * @property integer $id
 * @property string  $login
 * @property string  $email
 */
abstract class AbstractIdentity extends Row implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    abstract public function getPrivileges(): array;

    /**
     * {@inheritdoc}
     */
    public function getId(): ?int
    {
        return $this->id ? (int)$this->id : null;
    }

    /**
     * {@inheritdoc}
     */
    public function hasPrivilege($module, $privilege): bool
    {
        $privileges = $this->getPrivileges();

        return in_array("$module:$privilege", $privileges, true);
    }
}
