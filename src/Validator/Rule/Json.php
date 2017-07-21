<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Validator\Rule;

/**
 * Check for JSON
 *
 * @package Bluz\Validator\Rule
 */
class Json extends AbstractRule
{
    /**
     * @var string error template
     */
    protected $template = '{{name}} must be a valid JSON string';

    /**
     * Check for valid JSON string
     *
     * @param  string $input
     *
     * @return bool
     */
    public function validate($input): bool
    {
        return (bool)json_decode($input);
    }
}
