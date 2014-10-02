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

use Bluz\Common\Exception\ConfigurationException;
use Bluz\Common\Nil;
use Bluz\Proxy;

/**
 * Abstract session handler
 * @package Bluz\Session\Adapter
 */
abstract class AbstractAdapter
{
    /**
     * Instance of Redis
     * @var \Bluz\Cache\Cache
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
     * @param string $savePath
     * @param string $sessionName
     * @return bool|void
     */
    public function open($savePath, $sessionName)
    {
        $params = session_get_cookie_params();

        $this->prefix = $sessionName . ':';
        $this->ttl = $params['lifetime'];

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
