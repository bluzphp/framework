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
 * Check float
 *
 * @package Bluz\Validator\Rule
 */
class FloatRule extends AbstractRule
{
    /**
     * @var string error template
     */
    protected $description = 'must be a float number';

    /**
     * Check input data
     *
     * @param  string $input
     *
     * @return bool
     */
    public function validate($input): bool
    {
        return is_float(filter_var($input, FILTER_VALIDATE_FLOAT));
    }
}
