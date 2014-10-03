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
     *
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
     * @param string $id
     * @return bool|string
     */
    public function read($id)
    {
        $id = $this->prefix . $id;
        $data =  $this->handler->get($id);
        $this->handler->set($id, $data, $this->ttl);
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
        $this->handler->set($id, $data, $this->ttl);
    }

    /**
     * @param int $id
     * @return bool|void
     */
    public function destroy($id)
    {
        $this->handler->delete($this->prefix . $id);
    }
}
