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
 * Class Positive
 * @package Bluz\Validator\Rule
 */
class Positive extends AbstractRule
{
    /**
     * @var string Error template
     */
    protected $template = '{{name}} must be positive';

    /**
     * Check for positive number
     * @param string $input
     * @return bool
     */
    public function validate($input)
    {
        return $input > 0;
    }
}
