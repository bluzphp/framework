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
 * Check for iInteger
 *
 * @package Bluz\Validator\Rule
 */
class IntegerRule extends AbstractRule
{
    /**
     * @var string error template
     */
    protected $description = 'must be an integer number';

    /**
     * Check input data
     *
     * @param mixed $input
     *
     * @return bool
     */
    public function validate($input): bool
    {
        return is_numeric($input) && (int)$input == $input;
    }
}
