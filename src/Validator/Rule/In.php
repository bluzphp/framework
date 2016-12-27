<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Validator\Rule;

/**
 * Check for value in set
 *
 * @package Bluz\Validator\Rule
 */
class In extends AbstractRule
{
    /**
     * @var string|array haystack for search, can be array or string
     */
    protected $haystack;

    /**
     * @var bool strong comparison
     */
    protected $identical;

    /**
     * Setup validation rule
     *
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
     *
     * @param  string $input
     * @return bool
     */
    public function validate($input) : bool
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
     *
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
