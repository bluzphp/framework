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
 * Class Memcached
 * @package Bluz\Cache\Adapter
 */
class Memcached extends AbstractAdapter
{
    /**
     * Instance of memcached
     * @var \Memcached
     */
    protected $handler = null;

    /**
     * Check and setup memcached servers
     *
     * @param array $settings
     * @throws ComponentException
     * @throws ConfigurationException
     */
    public function __construct($settings = array())
    {
        // check Memcached extension
        if (!extension_loaded('memcached')) {
            throw new ComponentException(
                "Memcached extension not installed/enabled.
                Install and/or enable memcached extension. See phpinfo() for more information"
            );
        }

        // check Memcached settings
        if (!is_array($settings) or empty($settings) or !isset($settings['servers'])) {
            throw new ConfigurationException(
                "Memcached configuration is missed. Please check 'cache' configuration section"
            );
        }

        parent::__construct($settings);
    }

    /**
     * Get Mamcached Handler
     *
     * @return \Memcached
     */
    public function getHandler()
    {
        if (!$this->handler) {

            $persistentId = isset($this->settings['persistent']) ? $this->settings['persistent'] : null;

            $this->handler = new \Memcached($persistentId);

            if (!$this->handler->getServerList()) {
                $this->handler->addServers($this->settings['servers']);
            }

            if (isset($this->settings['options'])) {
                $this->handler->setOptions($this->settings['options']);
            }
        }
        return $this->handler;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $id
     * @return mixed
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
        return $this->getHandler()->add($id, $data, $ttl);
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
        return $this->getHandler()->set($id, $data, $ttl);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $id
     * @return bool
     */
    protected function doContains($id)
    {
        $this->getHandler()->get($id);
        return $this->getHandler()->getResultCode() !== \Memcached::RES_NOTFOUND;
    }

    /**
     * {@inheritdoc}
     *
     * @param string $id
     * @return bool
     */
    protected function doDelete($id)
    {
        return $this->getHandler()->delete($id);
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    protected function doFlush()
    {
        return $this->getHandler()->flush();
    }
}
