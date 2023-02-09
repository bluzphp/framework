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
use Bluz\Http\MimeType;

#[Attribute(Attribute::TARGET_FUNCTION | Attribute::IS_REPEATABLE)]
class Accept
{
    public function __construct(
        public MimeType $type
    ) {
    }
}
