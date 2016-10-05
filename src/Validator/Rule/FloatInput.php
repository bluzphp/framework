<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Validator\Rule;

/**
 * Check float
 *
 * @package Bluz\Validator\Rule
 */
class FloatInput extends AbstractRule
{
    /**
     * @var string error template
     */
    protected $template = '{{name}} must be a float number';

    /**
     * Check input data
     *
     * @param  string $input
     * @return bool
     */
    public function validate($input) : bool
    {
        return is_float(filter_var($input, FILTER_VALIDATE_FLOAT));
    }
}
