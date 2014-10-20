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
namespace Bluz\Common\Container;

/**
 * Container implements JsonSerializable interface
 * @see JsonSerializable
 *
 * @package  Bluz\Common
 *
 * @method array toArray()
 *
 * @author   Anton Shevchuk
 * @created  14.10.2014 10:15
 */
trait JsonSerialize
{
    /**
     * Specify data which should be serialized to JSON
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
