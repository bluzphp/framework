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

use Bluz\Validator\Exception\ComponentException;

/**
 * Class AbstractFilterRule
 * @package Bluz\Validator\Rule
 */
abstract class AbstractFilterRule extends AbstractRule
{
    /**
     * @var string Additional chars
     */
    protected $additionalChars = "";

    /**
     * Check input string
     * @param string $input
     * @return bool
     */
    abstract protected function validateClean($input);

    /**
     * Setup validation rule
     * @param string $additionalChars
     * @throws \Bluz\Validator\Exception\ComponentException
     */
    public function __construct($additionalChars = '')
    {
        if (!is_string($additionalChars)) {
            throw new ComponentException('Invalid list of additional characters to be loaded');
        }
        $this->additionalChars .= $additionalChars;
    }

    /**
     * Filter input data
     * @param string $input
     * @return string
     */
    protected function filter($input)
    {
        return str_replace(str_split($this->additionalChars), '', $input);
    }

    /**
     * Check input data
     * @param mixed $input
     * @return bool
     */
    public function validate($input)
    {
        if (!is_scalar($input)) {
            return false;
        }

        $cleanInput = $this->filter((string) $input);

        return $cleanInput === '' || $this->validateClean($cleanInput);
    }
}
