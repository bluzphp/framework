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
 * Check for no whitespaces
 *
 * @package Bluz\Validator\Rule
 */
class NoWhitespaceRule extends AbstractRule
{
    /**
     * @var string error template
     */
    protected $description = 'must not contain whitespace';

    /**
     * Check input data
     *
     * @param  string $input
     *
     * @return bool
     */
    public function validate($input) : bool
    {
        return is_null($input) || !preg_match('/\s/', (string)$input);
    }
}
