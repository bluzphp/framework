<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

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
 * @see      Instance::addNotice()
 *
 * @method   static Messages addSuccess($message, ...$text)
 * @see      Instance::addSuccess()
 *
 * @method   static Messages addError($message, ...$text)
 * @see      Instance::addError()
 *
 * @method   static integer count()
 * @see      Instance::count()
 *
 * @method   static \stdClass pop($type = null)
 * @see      Instance::pop()
 *
 * @method   static \ArrayObject popAll()
 * @see      Instance::popAll()
 *
 * @method   static void reset()
 * @see      Instance::reset()
 */
final class Messages
{
    use ProxyTrait;

    /**
     * Init instance
     *
     * @return Instance
     */
    private static function initInstance(): Instance
    {
        $instance = new Instance();
        $instance->setOptions(Config::get('messages'));
        return $instance;
    }
}
