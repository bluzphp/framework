<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Session\Store;

/**
 * Stub
 *
 * @category Bluz
 * @package  Session
 *
 * @author   Anton Shevchuk
 * @created  26.01.12 13:18
 */
class ArrayStore extends AbstractStore
{
    /**
     * @var array
     */
    protected $store = array();

    /**
     * start
     *
     * @return bool
     */
    public function start()
    {
        $this->store[$this->namespace] = array();
        return true;
    }

    /**
     * __set
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->store[$this->namespace][$key] = $value;
    }

    /**
     * __get
     *
     * @param string $key
     * @return mixed|null
     */
    public function __get($key)
    {
        return isset($this->store[$this->namespace][$key]) ? $this->store[$this->namespace][$key] : null;
    }


    /**
     * __isset
     *
     * @param string $key
     * @return boolean
     */
    public function __isset($key)
    {
        return isset($this->store[$this->namespace][$key]);
    }

    /**
     * __unset
     *
     * @param string $key
     * @return void
     */
    public function __unset($key)
    {
        unset($this->store[$this->namespace][$key]);
    }

    /**
     * destroy
     *
     * @return bool
     */
    public function destroy()
    {
        $this->store[$this->namespace] = array();
        return true;
    }
}
