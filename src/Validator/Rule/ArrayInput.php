<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Validator\Rule;

use Bluz\Validator\Exception\ComponentException;

/**
 * Check for array
 *
 * @package Bluz\Validator\Rule
 */
class ArrayInput extends AbstractRule
{
    /**
     * @var string error template
     */
    protected $template = '{{name}} must be a array';

    /**
     * @var callable Callback for check input array
     */
    protected $callback;

    /**
     * Setup validation rule
     *
     * @param  callable $callback
     * @throws ComponentException
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * Check input data
     *
     * @param  string $input
     * @return bool
     */
    public function validate($input) : bool
    {
        if (!is_array($input)) {
            return false;
        }
        $filtered = array_filter($input, $this->callback);
        return count($input) == count($filtered);
    }
}
