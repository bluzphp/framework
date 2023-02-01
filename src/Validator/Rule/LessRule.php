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
 * Check for maximum
 *
 * @package Bluz\Validator\Rule
 */
class LessRule extends AbstractCompareRule
{
    /**
     * @var mixed maximum value
     */
    protected $maxValue;

    /**
     * Setup validation rule
     *
     * @param mixed $maxValue
     */
    public function __construct($maxValue)
    {
        $this->maxValue = $maxValue;
    }

    /**
     * Check input data
     *
     * @param NumericRule $input
     *
     * @return bool
     */
    public function validate($input): bool
    {
        return $this->less($input, $this->maxValue);
    }

    /**
     * Get error template
     *
     * @return string
     */
    public function getDescription(): string
    {
        return __('must be lower than "%s"', $this->maxValue);
    }
}
