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
     * Set key/value pair
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * Get value by key
     *
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        } else {
            return null;
        }
    }

    /**
     * Isset
     *
     * @param string $key
     * @return bool
     */
    public function contains($key)
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * Unset
     *
     * @param string $key
     * @return void
     */
    public function delete($key)
    {
        unset($this->data[$key]);
    }

    /**
     * Set key/value pair
     *
     * @deprecated since version 0.5.1
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Get value by key
     *
     * @deprecated since version 0.5.1
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * Isset
     *
     * @deprecated since version 0.5.1
     * @param string $key
     * @return bool
     */
    public function __isset($key)
    {
        return $this->contains($key);
    }

    /**
     * Unset
     *
     * @deprecated since version 0.5.1
     * @param string $key
     * @return void
     */
    public function __unset($key)
    {
        $this->delete($key);
    }
}
