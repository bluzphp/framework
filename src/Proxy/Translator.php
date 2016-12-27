<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Proxy;

use Bluz\Translator\Translator as Instance;

/**
 * Proxy to Translator
 *
 * Example of usage
 * <code>
 *     use Bluz\Proxy\Translator;
 *
 *     echo Translator::translate('message id');
 * </code>
 *
 * @package  Bluz\Proxy
 * @author   Anton Shevchuk
 *
 * @method   static Instance getInstance()
 *
 * @method   static string translate($message, ...$text)
 * @see      Instance::translate()
 *
 * @method   static string translatePlural($singular, $plural, $number, ...$text)
 * @see      Instance::translatePlural()
 */
class Translator
{
    use ProxyTrait;

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
