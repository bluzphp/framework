<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Proxy;

use Bluz\Application\Application as Instance;
use Bluz\Common\Exception\ComponentException;

/**
 * Proxy to Application
 *
 * Example of usage
 * <code>
 *     use Bluz\Proxy\Application;
 *
 *     if (Application::isDebug()) {
 *          echo 'Debug message';
 *     }
 * </code>
 *
 * @package  Bluz\Proxy
 * @author   Anton Shevchuk
 *
 * @method   static Instance getInstance()
 *
 * @method   static bool isDebug()
 * @see      Instance::isDebug()
 *
 * @method   static string getPath()
 * @see      Instance::getPath()
 *
 * @method   static string getBaseUrl()
 * @see      Instance::getBaseUrl()
 *
 * @method   static string getEnvironment()
 * @see      Instance::getEnvironment()
 */
final class Application
{
    use ProxyTrait;

    /**
     * Init instance
     *
     * @throws ComponentException
     */
    private static function initInstance()
    {
        throw new ComponentException('Class `Proxy\\Application` required external initialization');
    }
}
