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
 * Check for latin character(s)
 *
 * @package Bluz\Validator\Rule
 */
class LatinRule extends AbstractFilterRule
{
    /**
     * Check for latin character(s)
     *
     * @param  mixed $input
     *
     * @return bool
     */
    public function validateClean($input) : bool
    {
        return (bool)preg_match('/^[a-z]+$/i', $input);
    }

    /**
     * Get error template
     *
     * @return string
     */
    public function getDescription() : string
    {
        if (empty($this->additionalChars)) {
            return __('must contain only Latin letters');
        }
        return __('must contain only Latin letters and "%s"', $this->additionalChars);
    }
}
