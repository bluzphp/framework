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
     * @param string $input
     * @return bool
     */
    protected function validateCtype($input)
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
            return __('"{{name}}" must contain only letters (a-z) and digits (0-9)');
        } else {
            return __('"{{name}}" must contain only letters (a-z), digits (0-9) and "%s"', $this->additionalChars);
        }
    }
}
