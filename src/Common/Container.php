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
namespace Bluz\Common;

/**
 * Container implements
 *  - \JsonSerializable
 *  - \ArrayAccess
 *
 * @package  Bluz\Common
 *
 * @author   Anton Shevchuk
 * @created  17.09.2014 16:03
 */
trait Container {

    /**
     * @var array
     */
    protected $container = array();

    /**
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->container[$key] = $value;
    }

    /**
     * @param  string $key
     * @return mixed
     */
    public function __get($key)
    {
        if (isset($this->$key)) {
            return $this->container[$key];
        } else {
            return null;
        }
    }
    
    /**
     * @param  string $key
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->container[$key]);
    }

    /**
     * @param  string $key
     * @return void
     */
    public function __unset($key)
    {
        unset($this->container[$key]);
    }

    /**
     * Sets all data in the row from an array
     *
     * @param  array $data
     * @return self
     */
    public function setFromArray(array $data)
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
        return $this;
    }

    /**
     * Returns the column/value data as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->container;
    }

    /**
     * Reset container array
     *
     * @return self
     */
    public function resetArray()
    {
        foreach ($this->container as &$value) {
            $value = null;
        }
        return $this;
    }

    /**
     * Implement JsonSerializable
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @throws \InvalidArgumentException
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            throw new \InvalidArgumentException('Class `Common\Container` support only associative arrays');
        } else {
            $this->__set($offset, $value);
        }
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->__isset($offset);
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        $this->__unset($offset);
    }

    /**
     * @param mixed $offset
     * @return string
     */
    public function offsetGet($offset)
    {
        return $this->__get($offset);
    }
}
