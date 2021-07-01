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
 * Check for minimum
 *
 * @package Bluz\Validator\Rule
 */
class MoreRule extends AbstractCompareRule
{
    /**
     * @var mixed minimum value
     */
    protected $minValue;

    /**
     * Setup validation rule
     *
     * @param mixed $minValue
     */
    public function __construct($minValue)
    {
        $this->minValue = $minValue;
    }

    /**
     * Check input data
     *
     * @param  NumericRule $input
     *
     * @return bool
     */
    public function validate($input): bool
    {
        return $this->less($this->minValue, $input);
    }

    /**
     * Get error template
     *
     * @return string
     */
    public function getDescription(): string
    {
        return __('must be greater than "%s"', $this->minValue);
    }
}
