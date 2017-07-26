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
 * Check for value in set
 *
 * @package Bluz\Validator\Rule
 */
class InRule extends AbstractRule
{
    /**
     * @var string|array haystack for search, can be array or string
     */
    protected $haystack;

    /**
     * Setup validation rule
     *
     * @param string|array $haystack
     */
    public function __construct($haystack)
    {
        $this->haystack = $haystack;
    }

    /**
     * Check input data
     *
     * @param  string $input
     *
     * @return bool
     */
    public function validate($input): bool
    {
        if (is_array($this->haystack)) {
            return in_array($input, $this->haystack, false);
        }

        if (!is_string($this->haystack)) {
            return false;
        }

        if (empty($input)) {
            return false;
        }

        $enc = mb_detect_encoding($input);

        return mb_stripos($this->haystack, $input, 0, $enc) !== false;
    }

    /**
     * Get error template
     *
     * @return string
     */
    public function getDescription() : string
    {
        if (is_array($this->haystack)) {
            $haystack = implode(', ', $this->haystack);
        } else {
            $haystack = $this->haystack;
        }
        return __('must be in %s', $haystack);
    }
}
