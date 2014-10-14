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
 * Container implements
 *  - \JsonSerializable
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
     * Implement JsonSerializable
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
