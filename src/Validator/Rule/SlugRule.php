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
 * Check for slug by regular expressions
 *
 * @package Bluz\Validator\Rule
 */
class SlugRule extends AbstractRule
{
    /**
     * @var string error template
     */
    protected $description = 'must be a valid slug';

    /**
     * Check input data
     *
     * @param  string $input
     *
     * @return bool
     */
    public function validate($input): bool
    {
        if (false !== strpos($input, '--')) {
            return false;
        }

        if (!preg_match('/^[0-9a-z\-]+$/', $input)) {
            return false;
        }

        if (preg_match('/^-|-$/', $input)) {
            return false;
        }

        return true;
    }
}
