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
     * @var bool
     */
    protected $inclusive;

    /**
     * $what less $than
     *
     * @param numeric $what
     * @param numeric $than
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
