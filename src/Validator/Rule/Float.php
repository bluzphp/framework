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
 * Class Float
 * @package Bluz\Validator\Rule
 */
class Float extends AbstractRule
{
    /**
     * @var string Error template
     */
    protected $template = '{{name}} must be a float number';

    /**
     * Check input data
     * @param string $input
     * @return bool
     */
    public function validate($input)
    {
        return is_float(filter_var($input, FILTER_VALIDATE_FLOAT));
    }
}
