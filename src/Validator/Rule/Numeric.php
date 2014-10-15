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
 * Class Numeric
 * @package Bluz\Validator\Rule
 */
class Numeric extends AbstractRule
{
    /**
     * @var string Error template
     */
    protected $template = '{{name}} must be numeric';

    /**
     * Check input data
     *
     * @param mixed $input
     * @return bool
     */
    public function validate($input)
    {
        return is_numeric($input);
    }
}
