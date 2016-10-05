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
 * Check input data by compare with some value
 *
 * @package Bluz\Validator\Rule
 */
class Equals extends AbstractRule
{
    /**
     * @var string string for compare
     */
    protected $compareTo;

    /**
     * @var bool strong comparison
     */
    protected $identical = false;

    /**
     * Setup validation rule
     *
     * @param string $compareTo
     * @param bool   $identical
     */
    public function __construct($compareTo, $identical = false)
    {
        $this->compareTo = $compareTo;
        $this->identical = $identical;
    }

    /**
     * Check input data
     *
     * @param  string $input
     * @return bool
     */
    public function validate($input) : bool
    {
        if ($this->identical) {
            return $input === $this->compareTo;
        } else {
            return $input == $this->compareTo;
        }
    }

    /**
     * Get error template
     *
     * @return string
     */
    public function getTemplate()
    {
        if ($this->identical) {
            return __('{{name}} must be identical as "%s"', $this->compareTo);
        } else {
            return __('{{name}} must be equals "%s"', $this->compareTo);
        }
    }
}
