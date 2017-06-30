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
 * Check for contains
 *
 * @package Bluz\Validator\Rule
 */
class Contains extends AbstractRule
{
    /**
     * @var string needle for search inside input data (haystack)
     */
    protected $containsValue;

    /**
     * @var bool strong comparison
     */
    protected $identical;

    /**
     * Setup validation rule
     *
     * @param mixed $containsValue
     * @param bool  $identical
     */
    public function __construct($containsValue, $identical = false)
    {
        $this->containsValue = $containsValue;
        $this->identical = $identical;
    }

    /**
     * Check input data
     *
     * @param  string|array $input
     *
     * @return bool
     */
    public function validate($input): bool
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
     *
     * @return string
     */
    public function getTemplate()
    {
        return __('{{name}} must contain the value "%s"', $this->containsValue);
    }
}
