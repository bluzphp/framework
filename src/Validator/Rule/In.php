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
 * Class In
 * @package Bluz\Validator\Rule
 */
class In extends AbstractRule
{
    /**
     * @var string|array Haystack for search, can be array or string
     */
    protected $haystack;

    /**
     * @var bool Strong comparison
     */
    protected $identical;

    /**
     * Setup validation rule
     * @param string|array $haystack
     * @param bool $identical
     */
    public function __construct($haystack, $identical = false)
    {
        $this->haystack = $haystack;
        $this->identical = $identical;
    }

    /**
     * Check input data
     * @param string $input
     * @return bool
     */
    public function validate($input)
    {
        if (is_array($this->haystack)) {
            return in_array($input, $this->haystack, $this->identical);
        }

        if (!is_string($this->haystack)) {
            return false;
        }

        if (empty($input)) {
            return false;
        }

        $enc = mb_detect_encoding($input);

        if ($this->identical) {
            return mb_strpos($this->haystack, $input, 0, $enc) !== false;
        }

        return mb_stripos($this->haystack, $input, 0, $enc) !== false;
    }

    /**
     * Get error template
     * @return string
     */
    public function getTemplate()
    {
        if (is_array($this->haystack)) {
            $haystack = join(', ', $this->haystack);
        } else {
            $haystack = $this->haystack;
        }
        return __('{{name}} must be in %s', $haystack);
    }
}
