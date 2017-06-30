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
 * Check for alphabetic character(s)
 *
 * @package Bluz\Validator\Rule
 */
class Alpha extends AbstractCtypeRule
{
    /**
     * Check for alphabetic character(s)
     *
     * @param  string $input
     *
     * @return bool
     */
    protected function validateClean($input): bool
    {
        return ctype_alpha($input);
    }

    /**
     * Get error template
     *
     * @return string
     */
    public function getTemplate()
    {
        if (empty($this->additionalChars)) {
            return __('{{name}} must contain only letters');
        } else {
            return __('{{name}} must contain only letters and "%s"', $this->additionalChars);
        }
    }
}
