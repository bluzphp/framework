<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Proxy;

use Bluz\Common\Exception\ComponentException;
use Bluz\Config\Config as Instance;

/**
 * Proxy to Config
 *
 * Example of usage
 * <code>
 *     use Bluz\Proxy\Config;
 *
 *     if (!Config::get('db')) {
 *          throw new Exception('Configuration for `db` is missed');
 *     }
 * </code>
 *
 * @package  Bluz\Proxy
 * @author   Anton Shevchuk
 *
 * @method   static Instance getInstance()
 *
 * @method   static mixed get(...$keys)
 * @see      Instance::get()
 */
final class Config
{
    use ProxyTrait;

    /**
     * Init instance
     *
     * @throws ComponentException
     */
    private static function initInstance()
    {
        throw new ComponentException('Class `Proxy\\Config` required external initialization');
    }
}
