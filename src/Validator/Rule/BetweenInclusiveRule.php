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
 * Check for value in range between minimum and maximum values
 *
 * @package Bluz\Validator\Rule
 */
class BetweenInclusiveRule extends BetweenRule
{
    /**
     * @var bool
     */
    protected $inclusive = true;

    /**
     * Get error template
     *
     * @return string
     */
    public function getDescription() : string
    {
        return __('must be inclusive between %1 and %2', $this->minValue, $this->maxValue);
    }
}
