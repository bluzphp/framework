<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Common\Container;

/**
 * Container implements JsonSerializable interface
 *
 * @package  Bluz\Common
 * @author   Anton Shevchuk
 * @see      JsonSerializable
 *
 * @method   array toArray()
 */
trait JsonSerialize
{
    /**
     * Specify data which should be serialized to JSON
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
