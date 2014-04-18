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

use Bluz\Session\SessionException;

/**
 * Session
 *
 * @package  Bluz\Session
 *
 * @author   Anton Shevchuk
 * @created  11.07.11 19:19
 */
class SessionStore extends AbstractStore
{
    /**
     * Start or not
     *
     * @var bool
     */
    protected $started = false;

    /**
     * Set session save path
     *
     * @param string $savePath
     * @throws \Bluz\Session\SessionException
     * @return SessionStore
     */
    public function setSavepath($savePath)
    {
        if (!is_dir($savePath)
            or !is_writable($savePath)
        ) {
            throw new SessionException('Session path is not writable');
        }
        session_save_path($savePath);
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Bluz\Session\SessionException
     * @return bool
     */
    public function start()
    {
        if (!$this->started) {
            if (headers_sent($filename, $linenum)) {
                throw new SessionException(
                    "Session must be started before any output has been sent to the browser;" .
                    " output started in {$filename}/{$linenum}"
                );
            } else {
                if (session_id() !== '') {
                    $this->started = true;
                } else {
                    $this->started = session_start();
                }
                if (!isset($_SESSION[$this->namespace])) {
                    $_SESSION[$this->namespace] = array();
                }
                return $this->started;
            }
        } else {
            return true;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public function __set($key, $value)
    {
        $this->start();
        $_SESSION[$this->namespace][$key] = $value;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $key
     * @param string $key
     * @return mixed|null
     */
    public function __get($key)
    {
        if (!$this->__isset($key)) {
            return null;
        }

        if (!isset($_SESSION[$this->namespace][$key])) {
            return null;
        }
        return $_SESSION[$this->namespace][$key];
    }

    /**
     * {@inheritdoc}
     *
     * @param string $key
     * @return boolean
     */
    public function __isset($key)
    {
        // to avoid implicit starting session on read empty session
        if (!$this->started && !$this->hasSessionId()) {
            return false;
        }
        $this->start();

        return isset($_SESSION[$this->namespace][$key]);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $key
     * @return void
     */
    public function __unset($key)
    {
        unset($_SESSION[$this->namespace][$key]);
    }

    /**
     * {@inheritdoc}
     *
     * @return boolean
     */
    public function destroy()
    {
        return session_destroy();
    }

    /**
     * Returns true if session ID is set.
     *
     * @return boolean
     */
    protected function hasSessionId()
    {
        return isset($_COOKIE[session_name()]);
    }
}
