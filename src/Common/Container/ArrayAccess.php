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
 * Container implements ArrayAccess
 * @see ArrayAccess
 *
 * @package  Bluz\Common
 *
 * @method void doSetContainer($key, $value)
 * @method mixed doGetContainer($key)
 * @method bool doContainsContainer($key)
 * @method void doDeleteContainer($key)
 *
 *
 * @author   Anton Shevchuk
 * @created  14.10.2014 10:11
 */
trait ArrayAccess
{
    /**
     * Offset to set
     * @param mixed $offset
     * @param mixed $value
     * @throws \InvalidArgumentException
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            throw new \InvalidArgumentException('Class `Common\Container\ArrayAccess` support only associative arrays');
        } else {
            $this->doSetContainer($offset, $value);
        }
    }

    /**
     * Offset to retrieve
     * @param mixed $offset
     * @return string
     */
    public function offsetGet($offset)
    {
        return $this->doGetContainer($offset);
    }

    /**
     * Whether a offset exists
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->doContainsContainer($offset);
    }

    /**
     * Offset to unset
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        $this->doDeleteContainer($offset);
    }
}
