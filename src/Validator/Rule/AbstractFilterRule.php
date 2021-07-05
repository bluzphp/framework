<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Validator\Rule;

use Bluz\Validator\Exception\ComponentException;

/**
 * Abstract rule for filters
 *
 * @package Bluz\Validator\Rule
 */
abstract class AbstractFilterRule extends AbstractRule
{
    /**
     * @var string additional chars
     */
    protected $additionalChars = '';

    /**
     * Check input string
     *
     * @param  string $input
     *
     * @return bool
     */
    abstract protected function validateClean($input);

    /**
     * Setup validation rule
     *
     * @param string $additionalChars
     */
    public function __construct(string $additionalChars = '')
    {
        $this->additionalChars .= $additionalChars;
    }

    /**
     * Filter input data
     *
     * @param string $input
     *
     * @return string
     */
    protected function filter(string $input): string
    {
        return str_replace(str_split($this->additionalChars), '', $input);
    }

    /**
     * Check input data
     *
     * @param  mixed $input
     *
     * @return bool
     */
    public function validate($input): bool
    {
        if (!is_scalar($input)) {
            return false;
        }

        $cleanInput = $this->filter((string)$input);

        return $cleanInput === '' || $this->validateClean($cleanInput);
    }
}
