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
 * Check for not empty
 *
 * @package Bluz\Validator\Rule
 */
class NotEmptyRule extends AbstractRule
{
    /**
     * @var string error template
     */
    protected $description = 'must not be empty';

    /**
     * Check input data
     *
     * @param  mixed $input
     *
     * @return bool
     */
    public function validate($input): bool
    {
        if (\is_string($input)) {
            $input = trim($input);
        }

        return !empty($input);
    }
}
