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
 * Class Equals
 * @package Bluz\Validator\Rule
 */
class Equals extends AbstractRule
{
    /**
     * @var string
     */
    protected $template = '{{name}} must be a version';

    /**
     * @var string
     */
    protected $compareTo;

    /**
     * @var bool
     */
    protected $identical = false;

    /**
     * @param string $compareTo
     * @param bool $identical
     */
    public function __construct($compareTo, $identical = false)
    {
        $this->compareTo = $compareTo;
        $this->identical = $identical;
    }

    /**
     * @param string $input
     * @return bool
     */
    public function validate($input)
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
            return __('"{{name}}" must be identical as "%s"', $this->compareTo);
        } else {
            return __('"{{name}}" must be equals "%s"', $this->compareTo);
        }
    }
}
