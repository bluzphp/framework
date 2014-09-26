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
namespace Bluz\Cache\Adapter;

use Bluz\Cache\Cache;
use Bluz\Common\Exception\ComponentException;
use Bluz\Common\Exception\ConfigurationException;
use Predis\Client;

/**
 * Redis cache adapter over Predis library
 * @package Bluz\Cache\Adapter
 * @link    https://github.com/nrk/predis/wiki
 * @author  Anton Shevchuk
 */
class Predis extends AbstractAdapter
{
    /**
     * Instance of Redis client
     * @var \Predis\Client
     */
    protected $handler = null;

    /**
     * Default Redis settings
     * @var array
     */
    protected $settings = array(
        'host' => '127.0.0.1',
        'port' => '6379',
        'timeout' => 5.0,
        'connection_persistent' => false,
        'options' => array()
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
        if (!class_exists('\\Predis\\Client')) {
            throw new ComponentException(
                "Predis library not found. Install Predis library [https://github.com/nrk/predis/wiki]"
            );
        }

        // check Redis settings
        if (!is_array($settings) or empty($settings)) {
            throw new ConfigurationException(
                "Predis configuration is missed. Please check 'cache' configuration section"
            );
        }

        parent::__construct($settings);
    }

    /**
     * Get Redis handler
     *
     * @return \Redis
     */
    public function getHandler()
    {
        if (!$this->handler) {
            $this->handler = new Client($this->settings, $this->settings['options']);
        }
        return $this->handler;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $id
     * @return bool|mixed|string
     */
    protected function doGet($id)
    {
        return unserialize($this->getHandler()->get($id));
    }

    /**
     * {@inheritdoc}
     *
     * @param string $id
     * @param mixed $data
     * @param int $ttl
     * @return bool
     */
    protected function doAdd($id, $data, $ttl = Cache::TTL_NO_EXPIRY)
    {
        if (!$this->doContains($id)) {
            $data = serialize($data);
            $this->doSet($id, $data, $ttl);
        }
        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $id
     * @param mixed $data
     * @param int $ttl
     * @return bool
     */
    protected function doSet($id, $data, $ttl = Cache::TTL_NO_EXPIRY)
    {
        $data = serialize($data);
        if (Cache::TTL_NO_EXPIRY == $ttl) {
            return $this->getHandler()->set($id, $data);
        } else {
            return $this->getHandler()->setex($id, $ttl, $data);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param string $id
     * @return bool
     */
    protected function doContains($id)
    {
        return $this->getHandler()->exists($id);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $id
     * @return int Number of keys deleted.
     */
    protected function doDelete($id)
    {
        return $this->getHandler()->del($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function doFlush()
    {
        return $this->getHandler()->flushAll();
    }
}
