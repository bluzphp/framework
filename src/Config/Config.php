<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Config;

use Bluz\Common\Collection;
use Bluz\Common\Container\Container;
use Bluz\Common\Container\RegularAccess;

/**
 * Config
 *
 * @package  Bluz\Config
 * @author   Anton Shevchuk
 * @link     https://github.com/bluzphp/framework/wiki/Config
 */
class Config
{
    use Container;
    use RegularAccess;

    /**
     * Return configuration by key
     *
     * @param array $keys
     *
     * @return array|mixed
     * @throws ConfigException
     */
    public function get(...$keys)
    {
        // configuration is missed
        if (empty($this->container)) {
            throw new ConfigException('System configuration is missing');
        }

        if (!\count($keys)) {
            return $this->container;
        }

        return Collection::get($this->container, ...$keys);
    }
}
