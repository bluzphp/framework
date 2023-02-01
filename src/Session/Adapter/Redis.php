<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Session\Adapter;

use Bluz\Common\Exception\ComponentException;
use Bluz\Common\Exception\ConfigurationException;

/**
 * Redis session handler
 *
 * @package Bluz\Session\Adapter
 */
class Redis extends AbstractAdapter implements \SessionHandlerInterface
{
    /**
     * @var array default Redis settings
     */
    protected $settings = [
        'host' => '127.0.0.1',
        'port' => 6379,
        'timeout' => 0,
        'persistence' => false,
    ];

    /**
     * Check and setup Redis server
     *
     * @param array $settings
     *
     * @throws ComponentException
     * @throws ConfigurationException
     */
    public function __construct(array $settings = [])
    {
        // check Redis extension
        if (!extension_loaded('redis')) {
            throw new ComponentException(
                'Redis extension not installed/enabled.
                Install and/or enable Redis extension [http://pecl.php.net/package/redis].
                See phpinfo() for more information'
            );
        }

        // check Redis settings
        if (!is_array($settings) || empty($settings)) {
            throw new ConfigurationException(
                'Redis configuration is missed. Please check `session` configuration section'
            );
        }

        // Update settings
        $this->settings = array_replace_recursive($this->settings, $settings);
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
        parent::open($savePath, $sessionName);

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

        return true;
    }

    /**
     * Read session data
     *
     * @param string $id
     *
     * @return string
     */
    public function read($id): string
    {
        return $this->handler->get($this->prepareId($id)) ?: '';
    }

    /**
     * Write session data
     *
     * @param string $id
     * @param string $data
     *
     * @return bool
     */
    public function write($id, $data): bool
    {
        return $this->handler->set($this->prepareId($id), $data, (int)$this->ttl);
    }

    /**
     * Destroy a session
     *
     * @param int $id
     *
     * @return bool
     */
    public function destroy($id): bool
    {
        $this->handler->del($this->prepareId($id));
        return true;
    }
}
