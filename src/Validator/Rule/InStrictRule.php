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
class InStrictRule extends InRule
{
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
            return in_array($input, $this->haystack, true);
        }

        if (!is_string($this->haystack)) {
            return false;
        }

        if (empty($input)) {
            return false;
        }

        $enc = mb_detect_encoding($input);

        return mb_strpos($this->haystack, $input, 0, $enc) !== false;
    }
}
