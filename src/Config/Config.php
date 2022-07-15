<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Config;

use Bluz\Collection\Collection;
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
     * @param string|array $key
     *
     * @return array|mixed
     * @throws ConfigException
     */
    public function get($key): mixed
    {
        // configuration is missed
        if (empty($this->container)) {
            throw new ConfigException('System configuration is missing');
        }

        if (!count($key)) {
            return $this->container;
        }

        return Collection::get($this->container, ...$key);
    }
}
