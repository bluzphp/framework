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
 * @package  Bluz\Proxy
 *
 * @method   static string getUrl($module = 'index', $controller = 'index', $params = array())
 * @method   static string getFullUrl($module = 'index', $controller = 'index', $params = array())
 *
 * @method   static void process()
 * @method   static string getDefaultModule()
 * @method   static string getDefaultController()
 * @method   static string getErrorModule()
 * @method   static string getErrorController()
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
        $instance->process();
        return $instance;
    }
}
