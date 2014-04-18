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
namespace Bluz\Session\Store;

/**
 * Stub
 *
 * @package  Bluz\Session
 *
 * @author   Anton Shevchuk
 * @created  26.01.12 13:18
 */
class ArrayStore extends AbstractStore
{
    /**
     * Session store in memory
     * @var array
     */
    protected $store = array();

    /**
     * Start session
     *
     * @return bool
     */
    public function start()
    {
        $this->store[$this->namespace] = array();
        return true;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     *
     * @param string $key
     * @return mixed|null
     */
    public function __get($key)
    {
        return isset($this->store[$this->namespace][$key]) ? $this->store[$this->namespace][$key] : null;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $key
     * @return boolean
     */
    public function __isset($key)
    {
        return isset($this->store[$this->namespace][$key]);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $key
     * @return void
     */
    public function __unset($key)
    {
        unset($this->store[$this->namespace][$key]);
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function destroy()
    {
        $this->store[$this->namespace] = array();
        return true;
    }
}
