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
 * Class Callback
 * @package Bluz\Validator\Rule
 */
class Callback extends AbstractRule
{
    /**
     * @var callable Callback for check input
     */
    protected $callback;

    /**
     * Setup validation rule
     * @param callable $callback
     * @throws \Bluz\Validator\Exception\ComponentException
     */
    public function __construct($callback)
    {
        if (!is_callable($callback)) {
            throw new ComponentException('Invalid callback function');
        }

        $this->callback = $callback;
    }

    /**
     * Check input data
     * @param mixed $input
     * @return bool
     */
    public function validate($input)
    {
        return (bool) call_user_func($this->callback, $input);
    }
}
