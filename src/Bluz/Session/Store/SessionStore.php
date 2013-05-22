<?php
/**
 * Copyright (c) 2013 by Bluz PHP Team
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * @namespace
 */
namespace Bluz\Session\Store;

use Bluz\Session\SessionException;

/**
 * Session
 *
 * @category Bluz
 * @package  Session
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
     * set session save path
     *
     * @param string $savePath
     * @throws \Bluz\Session\SessionException
     * @return SessionStore
     */
    public function setSavepath($savePath)
    {
        if (!is_dir($savePath)
            or !is_writable($savePath)) {
            throw new SessionException('Session path is not writable');
        }
        session_save_path($savePath);
        return $this;
    }

    /**
     * start
     *
     * @throws \Bluz\Session\SessionException
     * @return bool
     */
    public function start()
    {
        if (!$this->started) {
            if (headers_sent($filename, $linenum)) {
                throw new SessionException("Session must be started before any output has been sent to the browser;"
                    . " output started in {$filename}/{$linenum}");
            } else if (session_id() !== '') {
                throw new SessionException("Session has been started already");
            } else {
                $this->started = session_start();
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
     * set
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
     * get
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
     * __isset
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
     * __unset
     *
     * @param string $key
     * @return void
     */
    public function __unset($key)
    {
        unset($_SESSION[$this->namespace][$key]);
    }


    /**
     * destroy
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
