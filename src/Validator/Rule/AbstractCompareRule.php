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
 * Class AbstractFilterRule
 * @package Bluz\Validator\Rule
 */
abstract class AbstractCompareRule extends AbstractRule
{
    /**
     * @var bool Compare inclusive or not
     */
    protected $inclusive;

    /**
     * Check $what less $than or not
     * @param mixed $what
     * @param mixed $than
     * @return bool
     */
    protected function less($what, $than)
    {
        if ($this->inclusive) {
            return $what <= $than;
        } else {
            return $what < $than;
        }
    }
}
