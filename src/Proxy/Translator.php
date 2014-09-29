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

use Bluz\Translator\Translator as Instance;

/**
 * Proxy to Translator
 *
 * @package  Bluz\Proxy
 *
 * @method   static Instance getInstance()
 *
 * @method   static string translate($message)
 * @method   static string translatePlural($singular, $plural, $number)
 *
 * @author   Anton Shevchuk
 * @created  26.09.2014 16:32
 */
class Translator extends AbstractProxy
{
    /**
     * Init instance
     *
     * @return Instance
     */
    protected static function initInstance()
    {
        $instance = new Instance();
        $instance->setOptions(Config::getData('translator'));
        return $instance;
    }
}
