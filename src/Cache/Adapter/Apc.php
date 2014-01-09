<?php
/**
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
 * APC cache adapter
 * @author murzik
 */
class Apc extends AbstractAdapter
{
    /**
     * Check extension
     */
    public function __construct($settings = array())
    {
        if (!extension_loaded('apc')) {
            $msg = "APC extension not installed/enabled.
                    Install and/or enable APC extension. See phpinfo() for more information";
            throw new CacheException($msg);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function doGet($id)
    {
        return apc_fetch($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function doAdd($id, $data, $ttl = Cache::TTL_NO_EXPIRY)
    {
        return apc_add($id, $data, $ttl);
    }

    /**
     * {@inheritdoc}
     */
    protected function doSet($id, $data, $ttl = Cache::TTL_NO_EXPIRY)
    {
        return apc_store($id, $data, $ttl);
    }

    /**
     * {@inheritdoc}
     */
    protected function doContains($id)
    {
        return apc_exists($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function doDelete($id)
    {
        return apc_delete($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function doFlush()
    {
        return apc_clear_cache("user");
    }
}
