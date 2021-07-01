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
use Bluz\Router\Router as Instance;

/**
 * Proxy to Router
 *
 * Example of usage
 * <code>
 *     use Bluz\Proxy\Router;
 *
 *     Router::getUrl('pages', 'index', ['alias' => 'about']); // for skeleton application is `/about.html`
 * </code>
 *
 * @package  Bluz\Proxy
 * @author   Anton Shevchuk
 *
 * @method   static Instance getInstance()
 *
 * @method   static string getBaseUrl()
 * @see      Instance::getBaseUrl()
 * @method   static void   setBaseUrl($baseUrl)
 * @see      Instance::setBaseUrl()
 *
 * @method   static string getUrl($module = 'index', $controller = 'index', $params = [])
 * @see      Instance::getUrl()
 *
 * @method   static string getFullUrl($module = 'index', $controller = 'index', $params = [])
 * @see      Instance::getFullUrl()
 *
 * @method   static string getCleanUri()
 * @see      Instance::getCleanUri()
 *
 * @method   static void process()
 * @see      Instance::process()
 *
 * @method   static string getDefaultModule()
 * @see      Instance::getDefaultModule()
 *
 * @method   static string getDefaultController()
 * @see      Instance::getDefaultController()
 *
 * @method   static string getErrorModule()
 * @see      Instance::getErrorModule()
 *
 * @method   static string getErrorController()
 * @see      Instance::getErrorController()
 */
final class Router
{
    use ProxyTrait;

    /**
     * Init instance
     *
     * @throws ComponentException
     */
    private static function initInstance()
    {
        throw new ComponentException("Class `Proxy\\Router` required external initialization");
    }
}
