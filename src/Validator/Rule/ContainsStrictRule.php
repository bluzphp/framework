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
class ContainsStrictRule extends ContainsRule
{
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
            return in_array($this->containsValue, $input, true);
        }
        // for string
        if (is_string($input)) {
            return false !== mb_strpos($input, $this->containsValue, 0, mb_detect_encoding($input));
        }
        return false;
    }
}
