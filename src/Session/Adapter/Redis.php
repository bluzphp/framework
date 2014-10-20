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
class Redis extends AbstractAdapter implements \SessionHandlerInterface
{
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

    /**
     * Read session data
     * @param string $id
     * @return bool|string
     */
    public function read($id)
    {
        return $this->handler->get($this->prepareId($id));
    }

    /**
     * Write session data
     * @param string $id
     * @param string $data
     * @return bool|void
     */
    public function write($id, $data)
    {
        $this->handler->set($this->prepareId($id), $data, $this->ttl);
    }

    /**
     * Destroy a session
     * @param int $id
     * @return bool|void
     */
    public function destroy($id)
    {
        $this->handler->del($this->prepareId($id));
    }
}
