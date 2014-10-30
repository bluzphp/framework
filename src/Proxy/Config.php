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

use Bluz\Application\Application;
use Bluz\Config\Config as Instance;

/**
 * Proxy to Config
 *
 * Example of usage
 *     use Bluz\Proxy\Config;
 *
 *     if (!Config::getData('db')) {
 *          throw new Exception('Configuration for `db` is missed');
 *     }
 *
 * @package  Bluz\Proxy
 *
 * @method   static Instance getInstance()
 *
 * @method   static mixed getData($key = null, $section = null)
 * @see      Bluz\Config\Config::getData()
 *
 * @method   static mixed getModuleData($module, $section = null)
 * @see      Bluz\Config\Config::getModuleData()
 *
 * @author   Anton Shevchuk
 * @created  26.09.2014 13:06
 */
class Config extends AbstractProxy
{
    /**
     * Init instance
     *
     * @return Instance
     */
    protected static function initInstance()
    {
        $instance = new Instance();
        $instance->setPath(Application::getInstance()->getPath());
        $instance->setEnvironment(Application::getInstance()->getEnvironment());
        $instance->init();

        return $instance;
    }
}
