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
class Privilege
{
    public function __construct(
        public string $privilege
    ) {
    }
}
