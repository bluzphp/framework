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
 * Check for negative number
 *
 * @package Bluz\Validator\Rule
 */
class NegativeRule extends AbstractRule
{
    /**
     * @var string error template
     */
    protected $description = 'must be negative';

    /**
     * Check for negative number
     *
     * @param  string $input
     *
     * @return bool
     */
    public function validate($input) : bool
    {
        return $input < 0;
    }
}
