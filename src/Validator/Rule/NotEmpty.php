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
 * Class NotEmpty
 * @package Bluz\Validator\Rule
 */
class NotEmpty extends AbstractRule
{
    /**
     * @var string Error template
     */
    protected $template = '{{name}} must not be empty';

    /**
     * Check input data
     * @param mixed $input
     * @return bool
     */
    public function validate($input)
    {
        if (is_string($input)) {
            $input = trim($input);
        }

        return !empty($input);
    }
}
