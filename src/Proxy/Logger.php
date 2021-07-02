<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Proxy;

use Bluz\Common\Nil;
use Bluz\Logger\Logger as Instance;
use Exception;

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
 * @method   static void alert($message, array $context = [])
 * @see      Instance::alert()
 *
 * @method   static void critical($message, array $context = [])
 * @see      Instance::critical()
 *
 * @method   static void debug($message, array $context = [])
 * @see      Instance::debug()
 *
 * @method   static void emergency($message, array $context = [])
 * @see      Instance::emergency()
 *
 * @method   static void error($message, array $context = [])
 * @see      Instance::error()
 *
 * @method   static void info($message, array $context = [])
 * @see      Instance::info()
 *
 * @method   static void notice($message, array $context = [])
 * @see      Instance::notice()
 *
 * @method   static void warning($message, array $context = [])
 * @see      Instance::warning()
 *
 * @method   static void log($level, $message, array $context = [])
 * @see      Instance::log()
 *
 * @method   static array get($level)
 * @see      Instance::get()
 */
final class Logger
{
    use ProxyTrait;

    /**
     * Init instance
     *
     * @return Instance|Nil
     */
    private static function initInstance()
    {
        if (Config::get('logger')) {
            return new Instance();
        }
        return new Nil();
    }

    /**
     * exception
     *
     * @param Exception $exception
     *
     * @return void
     */
    public static function exception($exception): void
    {
        self::getInstance()->error(
            $exception->getMessage() . ' [' .
            $exception->getFile() . ':' .
            $exception->getLine() . ']'
        );
    }
}
