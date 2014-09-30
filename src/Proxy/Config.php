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
 * @package  Bluz\Proxy
 *
 * @method   static Instance getInstance()
 *
 * @method   static mixed getData($key = null, $section = null)
 * @method   static mixed getModuleData($module, $section = null)
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
