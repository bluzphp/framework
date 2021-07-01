<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Common\Container;

/**
 * Implements regular access to container
 *
 * @package  Bluz\Common
 * @author   Anton Shevchuk
 *
 * @method   void  doSetContainer($key, $value)
 * @method   mixed doGetContainer($key)
 * @method   bool  doContainsContainer($key)
 * @method   void  doDeleteContainer($key)
 */
trait RegularAccess
{
    /**
     * Set key/value pair
     *
     * @param  string $key
     * @param  mixed  $value
     *
     * @return void
     */
    public function set($key, $value): void
    {
        $this->doSetContainer($key, $value);
    }

    /**
     * Get value by key
     *
     * @param  string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        return $this->doGetContainer($key);
    }

    /**
     * Check contains key in container
     *
     * @param  string $key
     *
     * @return bool
     */
    public function contains($key): bool
    {
        return $this->doContainsContainer($key);
    }

    /**
     * Delete value by key
     *
     * @param  string $key
     *
     * @return void
     */
    public function delete($key): void
    {
        $this->doDeleteContainer($key);
    }
}
