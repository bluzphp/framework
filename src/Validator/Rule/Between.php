<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Validator\Rule;

use Bluz\Validator\Exception\ComponentException;

/**
 * Class Between
 * @package Bluz\Validator\Rule
 */
class Between extends AbstractCompareRule
{
    /**
     * @var mixed Minimum value
     */
    protected $minValue;

    /**
     * @var mixed Maximum value
     */
    protected $maxValue;

    /**
     * Setup validation rule
     * @param mixed $min
     * @param mixed $max
     * @param bool $inclusive
     * @throws \Bluz\Validator\Exception\ComponentException
     */
    public function __construct($min, $max, $inclusive = false)
    {
        $this->minValue = $min;
        $this->maxValue = $max;
        $this->inclusive = $inclusive;

        if (is_null($min) or is_null($max)) {
            throw new ComponentException('Minimum and maximum is required');
        }

        if ($min > $max) {
            throw new ComponentException(sprintf('%s cannot be less than %s for validation', $min, $max));
        }
    }

    /**
     * Check input data
     * @param numeric $input
     * @return bool
     */
    public function validate($input)
    {
        return $this->less($this->minValue, $input)
            && $this->less($input, $this->maxValue);
    }

    /**
     * Get error template
     * @return string
     */
    public function getTemplate()
    {
        return __('{{name}} must be between %1 and %2', $this->minValue, $this->maxValue);
    }
}
