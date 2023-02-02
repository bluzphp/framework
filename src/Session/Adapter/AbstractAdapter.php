<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Session\Adapter;

/**
 * Abstract session handler
 *
 * @package Bluz\Session\Adapter
 */
abstract class AbstractAdapter
{
    /**
     * @var mixed instance of Redis or Cache or some other
     */
    protected $handler = null;

    /**
     * @var string prefix for session store
     */
    protected string $prefix = 'PHPSESSID:';

    /**
     * @var integer TTL of session
     */
    protected int $ttl = 1800;

    /**
     * Prepare Id - add prefix
     *
     * @param string $id
     *
     * @return string
     */
    protected function prepareId($id): string
    {
        return $this->prefix . $id;
    }

    /**
     * Initialize session
     *
     * @param string $savePath
     * @param string $sessionName
     *
     * @return bool
     */
    public function open($savePath, $sessionName): bool
    {
        $this->prefix = $sessionName . ':';
        $this->ttl = (int)ini_get('session.gc_maxlifetime');

        // No more action necessary because connection is injected
        // in constructor and arguments are not applicable.

        return true;
    }

    /**
     * Close the session
     *
     * @return bool
     */
    public function close(): bool
    {
        $this->handler = null;
        unset($this->handler);
        return true;
    }

    /**
     * Cleanup old sessions
     *
     * @param int $maxLifetime
     *
     * @return bool
     */
    public function gc($maxLifetime): bool
    {
        // no action necessary because using EXPIRE
        return true;
    }
}
