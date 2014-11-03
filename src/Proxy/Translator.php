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
 * Example of usage
 *     use Bluz\Proxy\Translator;
 *
 *     echo Translator::translate('message id');
 *
 * @package  Bluz\Proxy
 *
 * @method   static Instance getInstance()
 *
 * @method   static string translate($message)
 * @see      Bluz\Translator\Translator::translate()
 *
 * @method   static string translatePlural($singular, $plural, $number)
 * @see      Bluz\Translator\Translator::translatePlural()
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
