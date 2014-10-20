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
 * Class AlphaNumeric
 * @package Bluz\Validator\Rule
 */
class AlphaNumeric extends AbstractCtypeRule
{
    /**
     * Check for alphanumeric character(s)
     * @param string $input
     * @return bool
     */
    protected function validateClean($input)
    {
        return ctype_alnum($input);
    }

    /**
     * Get error template
     * @return string
     */
    public function getTemplate()
    {
        if (empty($this->additionalChars)) {
            return __('{{name}} must contain only letters and digits');
        } else {
            return __('{{name}} must contain only letters, digits and "%s"', $this->additionalChars);
        }
    }
}
