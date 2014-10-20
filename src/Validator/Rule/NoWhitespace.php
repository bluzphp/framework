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
 * Class NoWhitespace
 * @package Bluz\Validator\Rule
 */
class NoWhitespace extends AbstractRule
{
    /**
     * @var string Error template
     */
    protected $template = '{{name}} must not contain whitespace';

    /**
     * Check input data
     * @param string $input
     * @return bool
     */
    public function validate($input)
    {
        return is_null($input) || !preg_match('/\s/', $input);
    }
}
