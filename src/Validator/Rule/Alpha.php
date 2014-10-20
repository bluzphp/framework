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
class Alpha extends AbstractCtypeRule
{
    /**
     * Check for alphabetic character(s)
     * @param string $input
     * @return bool
     */
    protected function validateClean($input)
    {
        return ctype_alpha($input);
    }

    /**
     * Get error template
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
