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
 * Check for numeric
 *
 * @package Bluz\Validator\Rule
 */
class NumericRule extends AbstractRule
{
    /**
     * @var string error template
     */
    protected $description = 'must be numeric';

    /**
     * Check for numeric
     *
     * @param  mixed $input
     *
     * @return bool
     */
    public function validate($input): bool
    {
        return is_numeric($input);
    }
}
