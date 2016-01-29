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
 * @see      Bluz\Router\Router::getBaseUrl()
 * @method   static void   setBaseUrl($baseUrl)
 * @see      Bluz\Router\Router::setBaseUrl()
 *
 * @method   static string getUrl($module = 'index', $controller = 'index', $params = array())
 * @see      Bluz\Router\Router::getUrl()
 *
 * @method   static string getFullUrl($module = 'index', $controller = 'index', $params = array())
 * @see      Bluz\Router\Router::getFullUrl()
 *
 * @method   static string getCleanUri()
 * @see      Bluz\Router\Router::getCleanUri()
 *
 * @method   static void process()
 * @see      Bluz\Router\Router::process()
 *
 * @method   static string getDefaultModule()
 * @see      Bluz\Router\Router::getDefaultModule()
 *
 * @method   static string getDefaultController()
 * @see      Bluz\Router\Router::getDefaultController()
 *
 * @method   static string getErrorModule()
 * @see      Bluz\Router\Router::getErrorModule()
 *
 * @method   static string getErrorController()
 * @see      Bluz\Router\Router::getErrorController()
 */
class Router extends AbstractProxy
{
    /**
     * Init instance
     *
     * @throws ComponentException
     */
    protected static function initInstance()
    {
        throw new ComponentException("Class `Proxy\\Router` required external initialization");
    }
}
