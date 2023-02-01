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
 * Check input by callback
 *
 * @package Bluz\Validator\Rule
 */
class CallbackRule extends AbstractRule
{
    /**
     * @var callable callback for check input
     */
    protected $callback;

    /**
     * Setup validation rule
     *
     * @param callable $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * Check input data
     *
     * @param mixed $input
     *
     * @return bool
     */
    public function validate($input): bool
    {
        return (bool)\call_user_func($this->callback, $input);
    }
}
