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
 * Class AbstractCtypeRule
 * @package Bluz\Validator\Rule
 */
abstract class AbstractCtypeRule extends AbstractFilterRule
{
    /**
     * @param string $input
     * @return bool
     */
    abstract protected function validateCtype($input);

    /**
     * @param string $input
     * @return string
     */
    protected function filter($input)
    {
        if (!empty($this->additionalChars)) {
            $input = str_replace(str_split($this->additionalChars), '', $input);
        }
        return preg_replace('/\s/', '', $input);
    }

    /**
     * @param $input
     * @return mixed
     */
    public function validateClean($input)
    {
        return $this->validateCtype($input);
    }
}
