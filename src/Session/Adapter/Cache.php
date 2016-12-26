<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Session\Adapter;

use Bluz\Common\Exception\ConfigurationException;
use Bluz\Proxy;

/**
 * Cache session handler
 *
 * @todo Migrate to {@link https://github.com/php-cache/session-handler PSR-6 Session handler}
 * @package Bluz\Session\Adapter
 */
class Cache extends AbstractAdapter implements \SessionHandlerInterface
{
    /**
     * Check and setup Redis server
     *
     * @param  array $settings
     * @throws ConfigurationException
     */
    public function __construct($settings = [])
    {
        if (!Proxy\Cache::getInstance()) {
            throw new ConfigurationException(
                "Cache configuration is missed or disabled. Please check 'cache' configuration section"
            );
        }
    }

    /**
     * Read session data
     *
     * @param  string $id
     * @return bool|string
     */
    public function read($id)
    {
        return Proxy\Cache::get($this->prepareId($id));
    }

    /**
     * Write session data
     *
     * @param  string $id
     * @param  string $data
     * @return bool|void
     */
    public function write($id, $data)
    {
        Proxy\Cache::set($this->prepareId($id), $data, $this->ttl);
    }

    /**
     * Destroy a session
     *
     * @param  integer $id
     * @return bool|void
     */
    public function destroy($id)
    {
        Proxy\Cache::delete($this->prepareId($id));
    }
}
