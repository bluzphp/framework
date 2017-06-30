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
 * Check for latin and numeric character(s)
 *
 * @package Bluz\Validator\Rule
 */
class LatinNumeric extends AbstractFilterRule
{
    /**
     * Check for latin and numeric character(s)
     *
     * @param  mixed $input
     *
     * @return bool
     */
    public function validateClean($input): bool
    {
        return (bool)preg_match('/^[a-z0-9]+$/i', $input);
    }

    /**
     * Get error template
     *
     * @return string
     */
    public function getTemplate()
    {
        if (empty($this->additionalChars)) {
            return __('{{name}} must contain only Latin letters and digits');
        } else {
            return __('{{name}} must contain only Latin letters, digits and "%s"', $this->additionalChars);
        }
    }
}
