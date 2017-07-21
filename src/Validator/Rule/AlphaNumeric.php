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
 * Check for alphanumeric character(s)
 *
 * @package Bluz\Validator\Rule
 */
class AlphaNumeric extends AbstractCtypeRule
{
    /**
     * Check for alphanumeric character(s)
     *
     * @param string $input
     *
     * @return bool
     */
    protected function validateClean($input): bool
    {
        return ctype_alnum($input);
    }

    /**
     * Get error template
     *
     * @return string
     */
    public function getTemplate()
    {
        if (empty($this->additionalChars)) {
            return __('{{name}} must contain only letters and digits');
        }
        return __('{{name}} must contain only letters, digits and "%s"', $this->additionalChars);
    }
}
