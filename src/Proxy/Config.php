<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Proxy;

use Bluz\Application\Application;
use Bluz\Config\Config as Instance;

/**
 * Proxy to Config
 *
 * Example of usage
 * <code>
 *     use Bluz\Proxy\Config;
 *
 *     if (!Config::getData('db')) {
 *          throw new Exception('Configuration for `db` is missed');
 *     }
 * </code>
 *
 * @package  Bluz\Proxy
 * @author   Anton Shevchuk
 *
 * @method   static Instance getInstance()
 *
 * @method   static mixed getData($key = null, $section = null)
 * @see      Instance::getData()
 *
 * @method   static mixed getModuleData($module, $section = null)
 * @see      Instance::getModuleData()
 */
final class Config
{
    use ProxyTrait;

    /**
     * Init instance
     *
     * @return Instance
     * @throws \Bluz\Config\ConfigException
     */
    private static function initInstance()
    {
        $instance = new Instance();
        $instance->setPath(Application::getInstance()->getPath());
        $instance->setEnvironment(Application::getInstance()->getEnvironment());
        $instance->init();

        return $instance;
    }
}
