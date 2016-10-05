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
 * Check for not empty
 *
 * @package Bluz\Validator\Rule
 */
class NotEmpty extends AbstractRule
{
    /**
     * @var string error template
     */
    protected $template = '{{name}} must not be empty';

    /**
     * Check input data
     *
     * @param  mixed $input
     * @return bool
     */
    public function validate($input) : bool
    {
        if (is_string($input)) {
            $input = trim($input);
        }

        return !empty($input);
    }
}
