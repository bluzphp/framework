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

use DateTime;

/**
 * Class Date
 * @package Bluz\Validator\Rule
 */
class Date extends AbstractRule
{
    /**
     * @var null Date format
     */
    protected $format = null;

    /**
     * Setup validation rule
     * @param null $format
     */
    public function __construct($format = null)
    {
        $this->format = $format;
    }

    /**
     * Check input data
     * @param mixed $input
     * @return bool
     */
    public function validate($input)
    {
        if ($input instanceof DateTime) {
            return true;
        } elseif (!is_string($input)) {
            return false;
        } elseif (is_null($this->format)) {
            return false !== strtotime($input);
        }

        $dateFromFormat = DateTime::createFromFormat($this->format, $input);

        return $dateFromFormat
               && $input === date($this->format, $dateFromFormat->getTimestamp());
    }

    /**
     * Get error template
     * @return string
     */
    public function getTemplate()
    {
        if ($this->format) {
            return __('{{name}} must be a valid date. Sample format: "%s"', $this->format);
        } else {
            return __('{{name}} must be a valid date');
        }
    }
}
