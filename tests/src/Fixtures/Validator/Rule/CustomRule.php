<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Tests\Fixtures\Validator\Rule;

use Bluz\Validator\Rule\AbstractRule;

/**
 * Check for iInteger
 *
 * @package Bluz\Validator\Rule
 */
class CustomRule extends AbstractRule
{
    /**
     * @var string error template
     */
    protected $description = 'must be a not empty custom string';

    /**
     * Check input data
     *
     * @param mixed $input
     *
     * @return bool
     */
    public function validate($input): bool
    {
        return is_string($input) && strlen($input) > 0;
    }
}
