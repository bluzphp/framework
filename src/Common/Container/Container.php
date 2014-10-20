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
 * Container of data for object
 *
 * @package  Bluz\Common
 *
 * @author   Anton Shevchuk
 * @created  17.09.2014 16:03
 */
trait Container
{
    /**
     * @var array Container of elements
     */
    protected $container = array();

    /**
     * Set key/value pair
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    protected function doSetContainer($key, $value)
    {
        $this->container[$key] = $value;
    }

    /**
     * Get value by key
     * @param string $key
     * @return mixed
     */
    protected function doGetContainer($key)
    {
        if ($this->doContainsContainer($key)) {
            return $this->container[$key];
        } else {
            return null;
        }
    }

    /**
     * Check contains key in container
     * @param string $key
     * @return bool
     */
    protected function doContainsContainer($key)
    {
        return array_key_exists($key, $this->container);
    }

    /**
     * Delete value by key
     * @param string $key
     * @return void
     */
    protected function doDeleteContainer($key)
    {
        unset($this->container[$key]);
    }

    /**
     * Sets all data in the row from an array
     * @param  array $data
     * @return self
     */
    public function setFromArray(array $data)
    {
        foreach ($data as $key => $value) {
            $this->container[$key] = $value;
        }
        return $this;
    }

    /**
     * Returns the column/value data as an array
     * @return array
     */
    public function toArray()
    {
        return $this->container;
    }

    /**
     * Reset container array
     * @return self
     */
    public function resetArray()
    {
        foreach ($this->container as &$value) {
            $value = null;
        }
        return $this;
    }
}
