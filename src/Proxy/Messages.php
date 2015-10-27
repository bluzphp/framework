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
 * <code>
 *     use Bluz\Proxy\Messages;
 *
 *     Messages::addSuccess('All Ok!');
 * </code>
 *
 * @package  Bluz\Proxy
 * @author   Anton Shevchuk
 *
 * @method   static Instance getInstance()
 *
 * @method   static Messages addNotice($message, ...$text)
 * @see      Bluz\Messages\Messages::addNotice()
 *
 * @method   static Messages addSuccess($message, ...$text)
 * @see      Bluz\Messages\Messages::addSuccess()
 *
 * @method   static Messages addError($message, ...$text)
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
