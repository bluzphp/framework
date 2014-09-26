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

/**
 * Redis cache adapter
 * @package Bluz\Cache\Adapter
 * @author  The-Who
 */
class Redis extends AbstractAdapter
{
    /**
     * Instance of Redis
     * @var \Redis
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
        'options' => array(
            \Redis::OPT_SERIALIZER => \Redis::SERIALIZER_PHP
        )
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
                "Redis configuration is missed. Please check 'cache' configuration section"
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
            $this->handler = new \Redis();
            if ($this->settings['connection_persistent']) {
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
     * {@inheritdoc}
     *
     * @param string $id
     * @return bool|mixed|string
     */
    protected function doGet($id)
    {
        return $this->getHandler()->get($id);
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
