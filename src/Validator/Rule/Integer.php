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
 * Check for iInteger
 *
 * @package Bluz\Validator\Rule
 */
class Integer extends AbstractRule
{
    /**
     * @var string error template
     */
    protected $template = '{{name}} must be an integer number';

    /**
     * Check input data
     *
     * @param  mixed $input
     * @return bool
     */
    public function validate($input) : bool
    {
        return is_numeric($input) && (int) $input == $input;
    }
}
