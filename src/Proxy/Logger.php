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

use Bluz\Logger\Logger as Instance;
use Bluz\Common\Nil;

/**
 * Proxy to Logger
 *
 * Example of usage
 *     use Bluz\Proxy\Logger;
 *
 *     Logger::error('Configuration not found');
 *
 * @package  Bluz\Proxy
 *
 * @method   static Instance getInstance()
 *
 * @method   static void alert($message, array $context = array())
 * @see      Bluz\Logger\Logger::alert()
 *
 * @method   static void critical($message, array $context = array())
 * @see      Bluz\Logger\Logger::critical()
 *
 * @method   static void debug($message, array $context = array())
 * @see      Bluz\Logger\Logger::debug()
 *
 * @method   static void emergency($message, array $context = array())
 * @see      Bluz\Logger\Logger::emergency()
 *
 * @method   static void error($message, array $context = array())
 * @see      Bluz\Logger\Logger::error()
 *
 * @method   static void info($message, array $context = array())
 * @see      Bluz\Logger\Logger::info()
 *
 * @method   static void notice($message, array $context = array())
 * @see      Bluz\Logger\Logger::notice()
 *
 * @method   static void warning($message, array $context = array())
 * @see      Bluz\Logger\Logger::warning()
 *
 * @method   static void log($level, $message, array $context = array())
 * @see      Bluz\Logger\Logger::log()
 *
 * @method   static array get($level)
 * @see      Bluz\Logger\Logger::get()
 *
 * @author   Anton Shevchuk
 * @created  26.09.2014 13:05
 */
class Logger extends AbstractProxy
{
    /**
     * Init instance
     *
     * @return Instance
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
