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

use Bluz\Common\Nil;
use Bluz\Logger\Logger as Instance;

/**
 * Proxy to Logger
 *
 * Example of usage
 * <code>
 *     use Bluz\Proxy\Logger;
 *
 *     Logger::error('Configuration not found');
 * </code>
 *
 * @package  Bluz\Proxy
 * @author   Anton Shevchuk
 *
 * @method   static Instance getInstance()
 *
 * @method   static void alert($message, array $context = array())
 * @see      Instance::alert()
 *
 * @method   static void critical($message, array $context = array())
 * @see      Instance::critical()
 *
 * @method   static void debug($message, array $context = array())
 * @see      Instance::debug()
 *
 * @method   static void emergency($message, array $context = array())
 * @see      Instance::emergency()
 *
 * @method   static void error($message, array $context = array())
 * @see      Instance::error()
 *
 * @method   static void info($message, array $context = array())
 * @see      Instance::info()
 *
 * @method   static void notice($message, array $context = array())
 * @see      Instance::notice()
 *
 * @method   static void warning($message, array $context = array())
 * @see      Instance::warning()
 *
 * @method   static void log($level, $message, array $context = array())
 * @see      Instance::log()
 *
 * @method   static array get($level)
 * @see      Instance::get()
 */
class Logger
{
    use ProxyTrait;

    /**
     * Init instance
     *
     * @return Instance|Nil
     */
    protected static function initInstance()
    {
        if (Config::getData('logger')) {
            return new Instance();
        } else {
            return new Nil();
        }
    }
}
