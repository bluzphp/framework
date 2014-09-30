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
 * @package  Bluz\Proxy
 *
 * @method   static Messages addNotice($text)
 * @method   static Messages addSuccess($text)
 * @method   static Messages addError($text)
 * @method   static integer count()
 * @method   static \stdClass pop($type = null)
 * @method   static \ArrayObject popAll()
 * @method   static void reset()
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
