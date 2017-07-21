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
class Min extends AbstractCompareRule
{
    /**
     * @var numeric minimum value
     */
    protected $minValue;

    /**
     * Setup validation rule
     *
     * @param numeric $minValue
     * @param bool    $inclusive
     */
    public function __construct($minValue, $inclusive = false)
    {
        $this->minValue = $minValue;
        $this->inclusive = $inclusive;
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
        return $this->less($this->minValue, $input);
    }

    /**
     * Get error template
     *
     * @return string
     */
    public function getTemplate()
    {
        if ($this->inclusive) {
            return __('{{name}} must be greater than or equals "%s"', $this->minValue);
        }
        return __('{{name}} must be greater than "%s"', $this->minValue);
    }
}
