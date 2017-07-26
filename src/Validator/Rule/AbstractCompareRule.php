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
 * Abstract rule of compare
 *
 * @package Bluz\Validator\Rule
 */
abstract class AbstractCompareRule extends AbstractRule
{
    /**
     * @var bool compare inclusive or not
     */
    protected $inclusive;

    /**
     * Check $what less $than or not
     *
     * @param  mixed $what
     * @param  mixed $than
     *
     * @return bool
     */
    protected function less($what, $than): bool
    {
        if ($this->inclusive) {
            return $what <= $than;
        }
        return $what < $than;
    }
}
