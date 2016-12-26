<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Common\Container;

/**
 * Implements magic access to container
 *
 * @package  Bluz\Common
 * @author   Anton Shevchuk
 *
 * @method   void  doSetContainer($key, $value)
 * @method   mixed doGetContainer($key)
 * @method   bool  doContainsContainer($key)
 * @method   void  doDeleteContainer($key)
 */
trait MagicAccess
{
    /**
     * Magic alias for set() regular method
     *
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->doSetContainer($key, $value);
    }

    /**
     * Magic alias for get() regular method
     *
     * @param  string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->doGetContainer($key);
    }

    /**
     * Magic alias for contains() regular method
     *
     * @param  string $key
     * @return bool
     */
    public function __isset($key)
    {
        return $this->doContainsContainer($key);
    }

    /**
     * Magic alias for delete() regular method
     *
     * @param  string $key
     * @return void
     */
    public function __unset($key)
    {
        $this->doDeleteContainer($key);
    }
}
