<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Validator\Rule;

/**
 * Check for positive number
 *
 * @package Bluz\Validator\Rule
 */
class PositiveRule extends AbstractRule
{
    /**
     * @var string error template
     */
    protected $description = 'must be positive';

    /**
     * Check for positive number
     *
     * @param  string $input
     *
     * @return bool
     */
    public function validate($input): bool
    {
        return is_numeric($input) && $input > 0;
    }
}
