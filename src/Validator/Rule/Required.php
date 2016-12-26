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
 * Check required
 *
 * @package Bluz\Validator\Rule
 */
class Required extends AbstractRule
{
    /**
     * @var string error template
     */
    protected $template = '{{name}} is required';

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

        return (false !== $input) && (null !== $input) && ('' !== $input);
    }
}
