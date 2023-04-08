<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/skeleton
 */

/**
 * @namespace
 */

namespace Bluz\Tests\Fixtures\Db\Model;

use Bluz\Db\TableInterface;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

/**
 * Test Row
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $status
 * @package  Bluz\Tests\Fixtures
 */
class Row extends \Bluz\Db\Row
{
    protected const STATUS_ACTIVE = 'active';
    protected const STATUS_DISABLED = 'disabled';
    protected const STATUS_DELETED = 'deleted';

    protected const STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_DISABLED,
        self::STATUS_DELETED
    ];

    #[Assert\Type('int')]
    protected int $id;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    protected string $name;

    #[Assert\NotBlank]
    #[Assert\Email]
    protected string $email = 'invalid';

    #[Assert\NotBlank]
    #[Assert\Choice(choices: self::STATUSES)]
    protected string $status = self::STATUS_ACTIVE;

    #[Assert\DateTime]
    protected string $created;

    #[Assert\DateTime]
    protected string $updated;
}
