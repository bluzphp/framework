<?php
/**
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
 * @category Bluz
 * @package  Session
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
     * @param string $key
     * @param mixed $value
     * @return void
     */
    abstract public function __set($key, $value);

    /**
     * @param string $key
     * @return mixed|null
     */
    abstract public function __get($key);

    /**
     * @param string $key
     * @return boolean
     */
    abstract public function __isset($key);

    /**
     * @param string $key
     * @return void
     */
    abstract public function __unset($key);

    /**
     * destroy
     *
     * @return bool
     */
    abstract public function destroy();
}
