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
 * Class Negative
 * @package Bluz\Validator\Rule
 */
class Negative extends AbstractRule
{
    /**
     * @var string Error template
     */
    protected $template = '{{name}} must be negative';

    /**
     * Check for negative number
     * @param string $input
     * @return bool
     */
    public function validate($input)
    {
        return $input < 0;
    }
}
