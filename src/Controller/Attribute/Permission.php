<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Controller\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_FUNCTION)]
class Permission
{
    public const NONE = 0;
    public const READ = 1;
    public const WRITE = 2;
    public const EXEC = 4;

    public function __construct(
        public int $permission
    ) {
    }
}
