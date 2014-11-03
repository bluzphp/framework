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

use Bluz\Router\Router as Instance;

/**
 * Proxy to Router
 *
 * Example of usage
 *     use Bluz\Proxy\Router;
 *
 *     Router::getUrl('pages', 'index', ['alias' => 'about']); // for skeleton application is `/about.html`
 *
 * @package  Bluz\Proxy
 *
 * @method   static Instance getInstance()
 *
 * @method   static string getUrl($module = 'index', $controller = 'index', $params = array())
 * @see      Bluz\Router\Router::getUrl()
 *
 * @method   static string getFullUrl($module = 'index', $controller = 'index', $params = array())
 * @see      Bluz\Router\Router::getFullUrl()
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
 *
 * @author   Anton Shevchuk
 * @created  29.09.2014 12:15
 */
class Router extends AbstractProxy
{
    /**
     * Init instance
     *
     * @return Instance
     */
    protected static function initInstance()
    {
        $instance = new Instance();
        $instance->setOptions(Config::getData('router'));
        return $instance;
    }
}
