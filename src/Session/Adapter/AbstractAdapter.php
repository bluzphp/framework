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
namespace Bluz\Session\Adapter;

/**
 * Abstract session handler
 * @package Bluz\Session\Adapter
 */
abstract class AbstractAdapter
{
    /**
     * Instance of Redis or Cache or some other
     * @var mixed
     */
    protected $handler = null;

    /**
     * @var string
     */
    protected $prefix = 'PHPSESSID:';

    /**
     * @var int ttl of session
     */
    protected $ttl = 1800;

    /**
     * Prepare Id - add prefix
     *
     * @param string $id
     * @return string
     */
    protected function prepareId($id)
    {
        return $this->prefix . $id;
    }

    /**
     * @param string $savePath
     * @param string $sessionName
     * @return bool|void
     */
    public function open($savePath, $sessionName)
    {
        $this->prefix = $sessionName . ':';
        $this->ttl = ini_get('session.gc_maxlifetime');

        // No more action necessary because connection is injected
        // in constructor and arguments are not applicable.
    }

    /**
     * @return bool|void
     */
    public function close()
    {
        $this->handler = null;
        unset($this->handler);
    }

    /**
     * @param int $maxLifetime
     * @return bool|void
     */
    public function gc($maxLifetime)
    {
        // no action necessary because using EXPIRE
    }
}
