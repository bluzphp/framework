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
 * Check for array
 *
 * @package Bluz\Validator\Rule
 */
class ArrayRule extends AbstractRule
{
    /**
     * @var string error template
     */
    protected $description = 'must be an array';

    /**
     * @var callable Callback for check input array
     */
    protected $callback;

    /**
     * Setup validation rule
     *
     * @param  callable $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
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
        if (!\is_array($input)) {
            return false;
        }
        $filtered = array_filter($input, $this->callback);
        return \count($input) === \count($filtered);
    }
}
