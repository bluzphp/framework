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
     * @var string
     */
    protected $template = '"{{name}}" must be negative';

    /**
     * @param string $input
     * @return bool
     */
    public function validate($input)
    {
        return $input < 0;
    }
}
