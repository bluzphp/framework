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
class Max extends AbstractCompareRule
{
    /**
     * @var numeric maximum value
     */
    protected $maxValue;

    /**
     * Setup validation rule
     *
     * @param numeric $maxValue
     * @param bool    $inclusive
     */
    public function __construct($maxValue, $inclusive = false)
    {
        $this->maxValue = $maxValue;
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
        return $this->less($input, $this->maxValue);
    }

    /**
     * Get error template
     *
     * @return string
     */
    public function getTemplate()
    {
        if ($this->inclusive) {
            return __('{{name}} must be lower than or equals "%s"', $this->maxValue);
        }
        return __('{{name}} must be lower than "%s"', $this->maxValue);
    }
}
