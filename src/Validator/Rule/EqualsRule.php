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
 * Check input data by compare with some value
 *
 * @package Bluz\Validator\Rule
 */
class EqualsRule extends AbstractRule
{
    /**
     * @var string string for compare
     */
    protected $compareTo;

    /**
     * Setup validation rule
     *
     * @param mixed $compareTo
     */
    public function __construct($compareTo)
    {
        $this->compareTo = $compareTo;
    }

    /**
     * Check input data
     *
     * @param  string $input
     *
     * @return bool
     */
    public function validate($input): bool
    {
        return $input == $this->compareTo;
    }

    /**
     * Get error template
     *
     * @return string
     */
    public function getDescription(): string
    {
        return __('must be equals "%s"', $this->compareTo);
    }
}
