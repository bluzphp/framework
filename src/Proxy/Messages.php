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

use Bluz\Messages\Messages as Instance;

/**
 * Proxy to Messages
 *
 * Example of usage
 *     use Bluz\Proxy\Messages;
 *
 *     Messages::addSuccess('All Ok!');
 *
 * @package  Bluz\Proxy
 *
 * @method   static Instance getInstance()
 *
 * @method   static Messages addNotice($text)
 * @see      Bluz\Messages\Messages::addNotice()
 *
 * @method   static Messages addSuccess($text)
 * @see      Bluz\Messages\Messages::addSuccess()
 *
 * @method   static Messages addError($text)
 * @see      Bluz\Messages\Messages::addError()
 *
 * @method   static integer count()
 * @see      Bluz\Messages\Messages::count()
 *
 * @method   static \stdClass pop($type = null)
 * @see      Bluz\Messages\Messages::pop()
 *
 * @method   static \ArrayObject popAll()
 * @see      Bluz\Messages\Messages::popAll()
 *
 * @method   static void reset()
 * @see      Bluz\Messages\Messages::reset()
 *
 * @author   Anton Shevchuk
 * @created  29.09.2014 12:15
 */
class Messages extends AbstractProxy
{
    /**
     * Init instance
     *
     * @return Instance
     */
    protected static function initInstance()
    {
        $instance = new Instance();
        $instance->setOptions(Config::getData('messages'));
        return $instance;
    }
}
