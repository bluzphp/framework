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

class Between extends AbstractCompareRule
{
    /**
     * @var null
     */
    protected $minValue;

    /**
     * @var null
     */
    protected $maxValue;

    /**
     * @param numeric $min
     * @param numeric $max
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
     * @param numeric $input
     * @return bool
     */
    public function validate($input)
    {
        return $this->less($this->minValue, $input)
            && $this->less($input, $this->maxValue);
    }

    /**
     * GetTemplate
     *
     * @return string
     */
    function getTemplate()
    {
        return __('{{name}} must be between %1 and %2', $this->minValue, $this->maxValue);
    }
}
