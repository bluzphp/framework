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
 * Proxy to Cache
 *
 * @package  Bluz\Proxy
 *
 * @method   static Instance getInstance()
 *
 * @method   static void alert($message, array $context = array())
 * @method   static void critical($message, array $context = array())
 * @method   static void debug($message, array $context = array())
 * @method   static void emergency($message, array $context = array())
 * @method   static void error($message, array $context = array())
 * @method   static void info($message, array $context = array())
 * @method   static void notice($message, array $context = array())
 * @method   static void warning($message, array $context = array())
 *
 * @method   static void log($level, $message, array $context = array())
 * @method   static array get($level)
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
