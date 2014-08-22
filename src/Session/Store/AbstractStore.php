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
     * Start or not
     *
     * @var bool
     */
    protected $started = false;

    /**
     * Session namespace
     *
     * @var string
     */
    protected $namespace = 'Bluz';

    /**
     * Set Namespace
     *
     * @param string $namespace
     * @return void
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * Start session
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
     * @return bool
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
