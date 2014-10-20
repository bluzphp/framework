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
 * Class Required
 * @package Bluz\Validator\Rule
 */
class Required extends AbstractRule
{
    /**
     * @var string Error template
     */
    protected $template = '{{name}} is required';

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

        return (false !== $input) && (null !== $input) && ('' !== $input);
    }
}
