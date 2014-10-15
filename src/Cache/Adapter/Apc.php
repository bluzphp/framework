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

/**
 * APC cache adapter
 *
 * @package Bluz\Cache\Adapter
 * @author  murzik
 */
class Apc extends AbstractAdapter
{
    /**
     * Check APC extension
     *
     * @param array $settings
     * @throws ComponentException
     */
    public function __construct($settings = array())
    {
        if (!extension_loaded('apc')) {
            $msg = "APC extension not installed/enabled.
                    Install and/or enable APC extension. See phpinfo() for more information";
            throw new ComponentException($msg);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param string $id
     * @return mixed
     */
    protected function doGet($id)
    {
        return apc_fetch($id);
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
        return apc_add($id, $data, $ttl);
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
        return apc_store($id, $data, $ttl);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $id
     * @return bool|string[]
     */
    protected function doContains($id)
    {
        return apc_exists($id);
    }

    /**
     * {@inheritdoc}
     *
     * @param string $id
     * @return bool|string[]
     */
    protected function doDelete($id)
    {
        return apc_delete($id);
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    protected function doFlush()
    {
        return apc_clear_cache("user");
    }
}
