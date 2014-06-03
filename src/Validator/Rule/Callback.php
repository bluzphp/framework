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

use Bluz\Validator\ValidatorException;

/**
 * Class Callback
 * @package Bluz\Validator\Rule
 */
class Callback extends AbstractRule
{
    /**
     * @var string
     */
    protected $template = '"%1$s" must be valid';

    /**
     * @var callable
     */
    protected $callback;

    /**
     * @param callable $callback
     */
    public function __construct($callback)
    {
        if (!is_callable($callback)) {
            throw new ValidatorException('Invalid callback');
        }

        $this->callback = $callback;
    }

    /**
     * @param mixed $input
     * @return bool
     */
    public function validate($input)
    {
        return (bool) call_user_func($this->callback, $input);
    }
}
