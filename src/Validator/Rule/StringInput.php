<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Validator\Rule;

/**
 * Check for string
 *
 * @package Bluz\Validator\Rule
 */
class StringInput extends AbstractRule
{
    /**
     * @var string error template
     */
    protected $template = '{{name}} must be a string';

    /**
     * Check input data
     *
     * @param  mixed $input
     * @return bool
     */
    public function validate($input) : bool
    {
        return is_string($input);
    }
}
