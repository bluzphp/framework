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
namespace Bluz\Session;

use Bluz\Common\Exception\ComponentException;
use Bluz\Common\Options;

/**
 * Session
 *
 * @package  Bluz\Session
 *
 * @author   Anton Shevchuk
 * @created  11.07.11 19:19
 *
 * @property mixed MessagesStore
 * @property \Bluz\Auth\EntityInterface identity Users\Row object
 * @property string agent - user agent
 */
class Session
{
    use Options;

    /**
     * @var string value returned by session_name()
     */
    protected $name;

    /**
     * @var string namespace
     */
    protected $namespace = 'bluz';

    /**
     * @var \SessionHandlerInterface Session save handler
     */
    protected $adapter;

    /**
     * Attempt to set the session name
     *
     * If the session has already been started, or if the name provided fails
     * validation, an exception will be raised.
     *
     * @param  string $name
     * @throws SessionException
     * @return Session
     */
    public function setName($name)
    {
        if ($this->sessionExists()) {
            throw new SessionException(
                'Cannot set session name after a session has already started'
            );
        }

        if (!preg_match('/^[a-zA-Z0-9]+$/', $name)) {
            throw new SessionException(
                'Name provided contains invalid characters; must be alphanumeric only'
            );
        }

        $this->name = $name;
        session_name($name);
        return $this;
    }

    /**
     * Get session name
     *
     * Proxies to {@link session_name()}.
     *
     * @return string
     */
    public function getName()
    {
        if (null === $this->name) {
            // If we're grabbing via session_name(), we don't need our
            // validation routine; additionally, calling setName() after
            // session_start() can lead to issues, and often we just need the name
            // in order to do things such as setting cookies.
            $this->name = session_name();
        }
        return $this->name;
    }

    /**
     * Set Namespace
     *
     * @param string $namespace
     * @return Session
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * Get Namespace
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Set session ID
     *
     * Can safely be called in the middle of a session.
     *
     * @param  string $id
     * @throws SessionException
     * @return Session
     */
    public function setId($id)
    {
        if ($this->sessionExists()) {
            throw new SessionException(
                'Session has already been started, to change the session ID call regenerateId()'
            );
        }
        session_id($id);
        return $this;
    }

    /**
     * Get session ID
     *
     * Proxies to {@link session_id()}
     *
     * @return string
     */
    public function getId()
    {
        return session_id();
    }

    /**
     * Regenerate id
     *
     * Regenerate the session ID, using session save handler's
     * native ID generation Can safely be called in the middle of a session.
     *
     * @param  bool $deleteOldSession
     * @return bool
     */
    public function regenerateId($deleteOldSession = true)
    {
        return session_regenerate_id((bool) $deleteOldSession);
    }

    /**
     * Returns true if session ID is set
     *
     * @return bool
     */
    public function cookieExists()
    {
        return isset($_COOKIE[session_name()]);
    }

    /**
     * Does a session started and is it currently active?
     *
     * @return bool
     */
    public function sessionExists()
    {
        $sid = defined('SID') ? constant('SID') : false;
        if ($sid !== false && $this->getId()) {
            return true;
        }
        if (headers_sent()) {
            return true;
        }
        return false;
    }

    /**
     * Start session
     *
     * if No session currently exists, attempt to start it. Calls
     * {@link isValid()} once session_start() is called, and raises an
     * exception if validation fails.
     *
     * @return void
     * @throws SessionException
     */
    public function start()
    {
        if ($this->sessionExists()) {
            return;
        }

        $this->initAdapter();

        session_start();
    }

    /**
     * Destroy/end a session
     *
     * @return void
     */
    public function destroy()
    {
        if (!$this->cookieExists() or !$this->sessionExists()) {
            return;
        }

        session_destroy();

        // send expire cookies
        $this->expireSessionCookie();

        // clear session data
        unset($_SESSION[$this->getNamespace()]);
    }

    /**
     * Set session save handler object
     *
     * @param  \SessionHandlerInterface $saveHandler
     * @return Session
     */
    public function setAdapter($saveHandler)
    {
        $this->adapter = $saveHandler;
        return $this;
    }

    /**
     * Get SaveHandler Object
     *
     * @return \SessionHandlerInterface
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * Register Save Handler with ext/session
     *
     * Since ext/session is coupled to this particular session manager
     * register the save handler with ext/session.
     *
     * @throws ComponentException
     * @return bool
     */
    protected function initAdapter()
    {
        if (is_null($this->adapter) or $this->adapter === 'files') {
            // try to apply settings
            if ($settings = $this->getOption('settings', 'files')) {
                $this->setSavePath($settings['save_path']);
            }
            return true;
        } elseif (is_string($this->adapter)) {
            $adapterClass = '\\Bluz\\Session\\Adapter\\'.ucfirst($this->adapter);
            if (!class_exists($adapterClass) or !is_subclass_of($adapterClass, '\SessionHandlerInterface')) {
                throw new ComponentException("Class for session adapter `{$this->adapter}` not found");
            }
            $settings = $this->getOption('settings', $this->adapter) ?: array();

            $this->adapter = new $adapterClass($settings);
        }

        return session_set_save_handler($this->adapter);
    }

    /**
     * Set the session cookie lifetime
     *
     * If a session already exists, destroys it (without sending an expiration
     * cookie), regenerates the session ID, and restarts the session.
     *
     * @param  int $ttl in seconds
     * @return void
     */
    public function setSessionCookieLifetime($ttl)
    {
        // Set new cookie TTL
        session_set_cookie_params($ttl);

        if ($this->sessionExists()) {
            // There is a running session so we'll regenerate id to send a new cookie
            $this->regenerateId();
        }
    }

    /**
     * Expire the session cookie
     *
     * Sends a session cookie with no value, and with an expiry in the past.
     *
     * @return void
     */
    public function expireSessionCookie()
    {
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                $this->getName(),
                '',
                $_SERVER['REQUEST_TIME'] - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
    }

    /**
     * Set session save path
     *
     * @param  string $savePath
     * @throws ComponentException
     * @return Session
     */
    protected function setSavePath($savePath)
    {
        if (!is_dir($savePath)
            or !is_writable($savePath)
        ) {
            throw new ComponentException('Session path is not writable');
        }
        session_save_path($savePath);
        return $this;
    }

    /**
     * Set key/value pair
     *
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function set($key, $value)
    {
        $this->start();
        // check storage
        if (!isset($_SESSION[$this->getNamespace()])) {
            $_SESSION[$this->getNamespace()] = array();
        }
        $_SESSION[$this->namespace][$key] = $value;
    }

    /**
     * Get value by key
     *
     * @param  string $key
     * @return mixed
     */
    public function get($key)
    {
        if ($this->contains($key)) {
            return $_SESSION[$this->namespace][$key];
        } else {
            return null;
        }
    }

    /**
     * Isset
     *
     * @param  string $key
     * @return bool
     */
    public function contains($key)
    {
        if ($this->cookieExists()) {
            $this->start();
            return isset($_SESSION[$this->namespace][$key]);
        } else {
            return false;
        }
    }

    /**
     * Unset
     *
     * @param  string $key
     * @return void
     */
    public function delete($key)
    {
        if ($this->cookieExists()) {
            $this->start();
            unset($_SESSION[$this->namespace][$key]);
        }
    }
}
