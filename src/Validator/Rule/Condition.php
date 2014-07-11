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
     * @var bool
     */
    protected $condition;

    /**
     * @param bool $condition
     */
    public function __construct($condition)
    {
        $this->condition = $condition;
    }

    /**
     * @param mixed $input
     * @return bool
     */
    public function validate($input)
    {
        return (bool) $this->condition;
    }
}
