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

use Bluz\EventManager\EventManager as Instance;

/**
 * Proxy to EventManager
 *
 * Example of usage
 *     use Bluz\Proxy\EventManager;
 *
 *     EvenManager::attach('event name', function() {
 *         // ... some logic
 *     });
 *
 *     EventManager::trigger('event name');
 *
 * @package  Bluz\Proxy
 *
 * @method   static Instance getInstance()
 *
 * @method   static Instance attach($eventName, $callback, $priority = 1)
 * @see      Bluz\EventManager\EventManager::attach()
 *
 * @method   static string|object trigger($event, $target = null, $params = null)
 * @see      Bluz\EventManager\EventManager::trigger()
 *
 * @author   Anton Shevchuk
 * @created  26.09.2014 18:35
 */
class EventManager extends AbstractProxy
{
    /**
     * Init instance
     *
     * @return Instance
     */
    protected static function initInstance()
    {
        return new Instance();
    }
}
