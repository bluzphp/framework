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
namespace Bluz\Registry;

use Bluz\Common\Options;

/**
 * Registry
 *
 * @package  Bluz\Registry
 *
 * @author   Anton Shevchuk
 */
class Registry
{
    use Options;

    /**
     * Stored data
     *
     * @var array
     */
    protected $data = array();

    /**
     * reset data
     *
     * @param array $data
     * @return Registry
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * exists key
     *
     * @param string $key
     * @return mixed
     */
    public function __isset($key)
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * setter for key
     *
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public function __set($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * getter for key
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        } else {
            return null;
        }
    }
}
