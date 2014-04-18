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

use Bluz\Common\Options;

/**
 * Abstract Session
 *
 * @package  Bluz\Session
 *
 * @author   Anton Shevchuk
 * @created  26.01.12 18:19
 */
abstract class AbstractStore
{
    use Options;

    /**
     * Session namespace
     *
     * @var string
     */
    protected $namespace = 'Bluz';

    /**
     * setNamespace
     *
     * @param string $namespace
     * @return AbstractStore
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * start
     *
     * @return bool
     */
    abstract public function start();

    /**
     * Set key/value pair
     * @param string $key
     * @param mixed $value
     * @return void
     */
    abstract public function __set($key, $value);

    /**
     * Get value by key
     * @param string $key
     * @return mixed|null
     */
    abstract public function __get($key);

    /**
     * Check key
     * @param string $key
     * @return boolean
     */
    abstract public function __isset($key);

    /**
     * Unset key
     * @param string $key
     * @return void
     */
    abstract public function __unset($key);

    /**
     * Destroy session
     * @return bool
     */
    abstract public function destroy();
}
