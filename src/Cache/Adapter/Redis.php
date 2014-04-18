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
use Bluz\Cache\CacheException;

/**
 * Redis cache adapter
 * @package Bluz\Cache\Adapter
 * @author The-Who
 */
class Redis extends AbstractAdapter
{
    /**
     * Instance of Redis
     * @var \Redis
     */
    protected $redis = null;

    /**
     * Default Redis settings
     * @var array
     */
    protected $settings = array(
        'host' => '127.0.0.1',
        'port' => '6379',
        'timeout' => null,
        'persistence' => false,
        'options' => array(
            \Redis::OPT_SERIALIZER => \Redis::SERIALIZER_PHP
        )
    );

    /**
     * Check and setup Redis server
     *
     * @param array $settings
     * @throws \Bluz\Cache\CacheException
     */
    public function __construct($settings = array())
    {
        if (!extension_loaded('redis')) {
            $msg = "Redis extension not installed/enabled.
                    Install and/or enable Redis extension [http://pecl.php.net/package/redis].
                    See phpinfo() for more information";
            throw new CacheException($msg);
        }

        // Check settings
        if (!is_array($settings) or empty($settings)) {
            throw new CacheException(
                "Redis configuration is missed.
                Please check 'cache' configuration section"
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
        if (!$this->redis) {
            $this->redis = new \Redis();
            if ($this->settings['persistence']) {
                $this->redis->pconnect($this->settings['host'], $this->settings['port'], $this->settings['timeout']);
            } else {
                $this->redis->connect($this->settings['host'], $this->settings['port'], $this->settings['timeout']);
            }
            if (isset($this->settings['options'])) {
                foreach ($this->settings['options'] as $key => $value) {
                    $this->redis->setOption($key, $value);
                }
            }
        }
        return $this->redis;
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
     * @return bool|mixed
     */
    protected function doAdd($id, $data, $ttl = Cache::TTL_NO_EXPIRY)
    {
        if (!$this->doContains($id)) {
            if (Cache::TTL_NO_EXPIRY == $ttl) {
                return $this->getHandler()->set($id, $data);
            } else {
                return $this->getHandler()->setex($id, $ttl, $data);
            }
        }
        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $id
     * @param mixed $data
     * @param int $ttl
     * @return bool|mixed
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
     * @return bool|mixed
     */
    protected function doContains($id)
    {
        return $this->getHandler()->exists($id);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $id
     * @return void
     */
    protected function doDelete($id)
    {
        $this->getHandler()->delete($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function doFlush()
    {
        return $this->getHandler()->flushAll();
    }
}
