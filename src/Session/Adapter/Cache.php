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
 * Cache session handler
 * @package Bluz\Session\Adapter
 */
class Cache extends AbstractAdapter implements \SessionHandlerInterface
{
    /**
     * Check and setup Redis server
     * @param array $settings
     * @throws ConfigurationException
     */
    public function __construct($settings = array())
    {
        $this->handler = Proxy\Cache::getInstance();

        if ($this->handler instanceof Nil) {
            throw new ConfigurationException(
                "Cache configuration is missed or disabled. Please check 'cache' configuration section"
            );
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
        $this->handler->delete($this->prepareId($id));
    }
}
