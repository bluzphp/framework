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
 * Class Contains
 * @package Bluz\Validator\Rule
 */
class Contains extends AbstractRule
{
    /**
     * @var string Needle for search inside input data (haystack)
     */
    protected $containsValue;

    /**
     * @var bool Strong comparison
     */
    protected $identical;

    /**
     * Setup validation rule
     * @param $containsValue
     * @param bool $identical
     */
    public function __construct($containsValue, $identical = false)
    {
        $this->containsValue = $containsValue;
        $this->identical = $identical;
    }

    /**
     * Check input data
     * @param string|array $input
     * @return bool
     */
    public function validate($input)
    {
        // for array
        if (is_array($input)) {
            return in_array($this->containsValue, $input, $this->identical);
        }

        // for string
        if ($this->identical) {
            return false !== mb_strpos($input, $this->containsValue, 0, mb_detect_encoding($input));
        } else {
            return false !== mb_stripos($input, $this->containsValue, 0, mb_detect_encoding($input));
        }
    }

    /**
     * Get error template
     * @return string
     */
    public function getTemplate()
    {
        return __('{{name}} must contain the value "%s"', $this->containsValue);
    }
}
