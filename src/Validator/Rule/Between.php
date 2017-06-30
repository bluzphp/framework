<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Validator\Rule;

use Bluz\Validator\Exception\ComponentException;

/**
 * Check for value in range between minimum and maximum values
 *
 * @package Bluz\Validator\Rule
 */
class Between extends AbstractCompareRule
{
    /**
     * @var mixed minimum value
     */
    protected $minValue;

    /**
     * @var mixed maximum value
     */
    protected $maxValue;

    /**
     * Setup validation rule
     *
     * @param  mixed $min
     * @param  mixed $max
     * @param  bool  $inclusive
     *
     * @throws \Bluz\Validator\Exception\ComponentException
     */
    public function __construct($min, $max, $inclusive = false)
    {
        $this->minValue = $min;
        $this->maxValue = $max;
        $this->inclusive = $inclusive;

        if (is_null($min) || is_null($max)) {
            throw new ComponentException('Minimum and maximum is required');
        }

        if ($min > $max) {
            throw new ComponentException(sprintf('%s cannot be less than %s for validation', $min, $max));
        }
    }

    /**
     * Check input data
     *
     * @param  numeric $input
     *
     * @return bool
     */
    public function validate($input): bool
    {
        return $this->less($this->minValue, $input)
            && $this->less($input, $this->maxValue);
    }

    /**
     * Get error template
     *
     * @return string
     */
    public function getTemplate()
    {
        return __('{{name}} must be between %1 and %2', $this->minValue, $this->maxValue);
    }
}
