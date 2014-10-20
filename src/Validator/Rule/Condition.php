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
 * Class String
 * @package Bluz\Validator\Rule
 */
class Condition extends AbstractRule
{
    /**
     * @var bool Condition rule
     */
    protected $condition;

    /**
     * Setup validation rule
     * @param bool $condition
     */
    public function __construct($condition)
    {
        $this->condition = $condition;
    }

    /**
     * Check input data
     * @param mixed $input
     * @return bool
     */
    public function validate($input)
    {
        return (bool) $this->condition;
    }
}
