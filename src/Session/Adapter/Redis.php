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

use Bluz\Common\Exception\ComponentException;
use Bluz\Common\Exception\ConfigurationException;

/**
 * Redis session handler
 * @package Bluz\Session\Adapter
 */
class Redis implements \SessionHandlerInterface
{
    /**
     * Instance of Redis
     * @var \Redis
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
     * Default Redis settings
     * @var array
     */
    protected $settings = array(
        'host' => '127.0.0.1',
        'port' => '6379',
        'timeout' => null,
        'persistence' => false,
    );

    /**
     * Check and setup Redis server
     *
     * @param array $settings
     * @throws ComponentException
     * @throws ConfigurationException
     */
    public function __construct($settings = array())
    {
        // check Redis extension
        if (!extension_loaded('redis')) {
            throw new ComponentException(
                "Redis extension not installed/enabled.
                Install and/or enable Redis extension [http://pecl.php.net/package/redis].
                See phpinfo() for more information"
            );
        }

        // check Redis settings
        if (!is_array($settings) or empty($settings)) {
            throw new ConfigurationException(
                "Redis configuration is missed. Please check 'session' configuration section"
            );
        }

        $this->settings = array_replace_recursive($this->settings, $settings);
    }

    /**
     * Get Redis handler
     *
     * @return \Redis
     */
    protected function getHandler()
    {
        if (!$this->handler) {
            $this->handler = new \Redis();
            if ($this->settings['persistence']) {
                $this->handler->pconnect($this->settings['host'], $this->settings['port'], $this->settings['timeout']);
            } else {
                $this->handler->connect($this->settings['host'], $this->settings['port'], $this->settings['timeout']);
            }
            if (isset($this->settings['options'])) {
                foreach ($this->settings['options'] as $key => $value) {
                    $this->handler->setOption($key, $value);
                }
            }
        }
        return $this->handler;
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
     * @param string $id
     * @return bool|string
     */
    public function read($id)
    {
        $id = $this->prefix . $id;
        $data = $this->getHandler()->get($id);
        $this->getHandler()->expire($id, $this->ttl);
        return $data;
    }

    /**
     * @param string $id
     * @param string $data
     * @return bool|void
     */
    public function write($id, $data)
    {
        $id = $this->prefix . $id;
        $this->getHandler()->set($id, $data);
        $this->getHandler()->expire($id, $this->ttl);
    }

    /**
     * @param int $id
     * @return bool|void
     */
    public function destroy($id)
    {
        $this->getHandler()->del($this->prefix . $id);
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
