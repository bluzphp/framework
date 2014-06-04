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

/**
 * Class Min
 * @package Bluz\Validator\Rule
 */
class Min extends AbstractRule
{
    /**
     * @var numeric
     */
    protected $minValue;

    /**
     * @var bool
     */
    protected $inclusive;

    /**
     * @param numeric $minValue
     * @param bool $inclusive
     */
    public function __construct($minValue, $inclusive = false)
    {
        $this->minValue = $minValue;
        $this->inclusive = $inclusive;
    }

    /**
     * @param numeric $input
     * @return bool
     */
    public function validate($input)
    {
        if ($this->inclusive) {
            return $input >= $this->minValue;
        } else {
            return $input > $this->minValue;
        }
    }

    /**
     * Get error template
     *
     * @return string
     */
    public function getTemplate()
    {
        if ($this->inclusive) {
            return __('"{{name}}" must be greater than or equals "%s"', $this->minValue);
        } else {
            return __('"{{name}}" must be greater than "%s"', $this->minValue);
        }
    }
}
