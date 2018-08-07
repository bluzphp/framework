<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Session;

use Bluz\Common\Exception\ComponentException;
use Bluz\Common\Options;

/**
 * Session
 *
 * @package  Bluz\Session
 * @author   Anton Shevchuk
 * @link     https://github.com/bluzphp/framework/wiki/Session
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
     *
     * @throws SessionException
     * @return void
     */
    public function setName($name) : void
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
    }

    /**
     * Get session name
     *
     * Proxies to {@link session_name()}.
     *
     * @return string
     */
    public function getName() : string
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
     * @param  string $namespace
     *
     * @return void
     */
    public function setNamespace(string $namespace) : void
    {
        $this->namespace = $namespace;
    }

    /**
     * Get Namespace
     *
     * @return string
     */
    public function getNamespace() : string
    {
        return $this->namespace;
    }

    /**
     * Set session ID
     *
     * Can safely be called in the middle of a session.
     *
     * @param  string $id
     *
     * @return void
     * @throws SessionException
     */
    public function setId($id) : void
    {
        if ($this->sessionExists()) {
            throw new SessionException(
                'Session has already been started, to change the session ID call regenerateId()'
            );
        }
        session_id($id);
    }

    /**
     * Get session ID
     *
     * Proxies to {@link session_id()}
     *
     * @return string
     */
    public function getId() : string
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
     *
     * @return bool
     */
    public function regenerateId($deleteOldSession = true) : bool
    {
        if ($this->sessionExists() && session_id() !== '') {
            return session_regenerate_id((bool)$deleteOldSession);
        }
        return false;
    }

    /**
     * Returns true if session ID is set
     *
     * @return bool
     */
    public function cookieExists() : bool
    {
        return isset($_COOKIE[session_name()]);
    }

    /**
     * Does a session started and is it currently active?
     *
     * @return bool
     */
    public function sessionExists() : bool
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }

    /**
     * Start session
     *
     * if No session currently exists, attempt to start it. Calls
     * {@link isValid()} once session_start() is called, and raises an
     * exception if validation fails.
     *
     * @return bool
     * @throws ComponentException
     */
    public function start() : bool
    {
        if ($this->sessionExists()) {
            return true;
        }

        if ($this->initAdapter()) {
            return session_start();
        }

        throw new ComponentException('Invalid adapter settings');
    }

    /**
     * Destroy/end a session
     *
     * @return void
     */
    public function destroy() : void
    {
        if (!$this->cookieExists() || !$this->sessionExists()) {
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
     *
     * @return void
     */
    public function setAdapter($saveHandler) : void
    {
        $this->adapter = $saveHandler;
    }

    /**
     * Get SaveHandler Object
     *
     * @return \SessionHandlerInterface
     */
    public function getAdapter() : \SessionHandlerInterface
    {
        return $this->adapter;
    }

    /**
     * Register Save Handler with ext/session
     *
     * Since ext/session is coupled to this particular session manager
     * register the save handler with ext/session.
     *
     * @return bool
     * @throws ComponentException
     */
    protected function initAdapter() : bool
    {
        if (null === $this->adapter || 'files' === $this->adapter) {
            // try to apply settings
            if ($settings = $this->getOption('settings', 'files')) {
                $this->setSavePath($settings['save_path']);
            }
            return true;
        }
        if (\is_string($this->adapter)) {
            $adapterClass = '\\Bluz\\Session\\Adapter\\' . ucfirst($this->adapter);
            if (!class_exists($adapterClass) || !is_subclass_of($adapterClass, \SessionHandlerInterface::class)) {
                throw new ComponentException("Class for session adapter `{$this->adapter}` not found");
            }
            $settings = $this->getOption('settings', $this->adapter) ?: [];

            $this->adapter = new $adapterClass($settings);
            return session_set_save_handler($this->adapter);
        }
        return true;
    }

    /**
     * Set the session cookie lifetime
     *
     * If a session already exists, destroys it (without sending an expiration
     * cookie), regenerates the session ID, and restarts the session.
     *
     * @param  integer $ttl TTL in seconds
     *
     * @return void
     */
    public function setSessionCookieLifetime($ttl) : void
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
    public function expireSessionCookie() : void
    {
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                $this->getName(),
                '',
                $_SERVER['REQUEST_TIME'] - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
    }

    /**
     * Set session save path
     *
     * @param  string $savePath
     *
     * @return void
     * @throws ComponentException
     */
    protected function setSavePath($savePath) : void
    {
        if (!is_dir($savePath)
            || !is_writable($savePath)
        ) {
            throw new ComponentException('Session path is not writable');
        }
        session_save_path($savePath);
    }

    /**
     * Set key/value pair
     *
     * @param  string $key
     * @param  mixed  $value
     *
     * @return void
     * @throws ComponentException
     */
    public function set($key, $value) : void
    {
        $this->start();
        // check storage
        if (!isset($_SESSION[$this->getNamespace()])) {
            $_SESSION[$this->getNamespace()] = [];
        }
        $_SESSION[$this->namespace][$key] = $value;
    }

    /**
     * Get value by key
     *
     * @param  string $key
     *
     * @return mixed
     * @throws ComponentException
     */
    public function get($key)
    {
        if ($this->contains($key)) {
            return $_SESSION[$this->namespace][$key];
        }
        return null;
    }

    /**
     * Isset
     *
     * @param  string $key
     *
     * @return bool
     * @throws ComponentException
     */
    public function contains($key) : bool
    {
        if ($this->cookieExists()) {
            $this->start();
        } elseif (!$this->sessionExists()) {
            return false;
        }
        return isset($_SESSION[$this->namespace][$key]);
    }

    /**
     * Unset
     *
     * @param  string $key
     *
     * @return void
     * @throws ComponentException
     */
    public function delete($key)
    {
        if ($this->contains($key)) {
            unset($_SESSION[$this->namespace][$key]);
        }
    }
}
