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
class ContainsRule extends AbstractRule
{
    /**
     * @var string needle for search inside input data (haystack)
     */
    protected $containsValue;

    /**
     * Setup validation rule
     *
     * @param mixed $containsValue
     */
    public function __construct($containsValue)
    {
        $this->containsValue = $containsValue;
    }

    /**
     * Check input data
     *
     * @param string|array $input
     *
     * @return bool
     */
    public function validate($input): bool
    {
        // for array
        if (is_array($input)) {
            return in_array($this->containsValue, $input, false);
        }
        // for string
        if (is_string($input)) {
            return false !== mb_stripos($input, $this->containsValue, 0, mb_detect_encoding($input));
        }
        // can't compare
        return false;
    }

    /**
     * Get error template
     *
     * @return string
     */
    public function getDescription(): string
    {
        return __('must contain the value "%s"', $this->containsValue);
    }
}
