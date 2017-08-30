<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Proxy;

use Bluz\EventManager\EventManager as Instance;

/**
 * Proxy to EventManager
 *
 * Example of usage
 * <code>
 *     use Bluz\Proxy\EventManager;
 *
 *     EvenManager::attach('event name', function() {
 *         // ... some logic
 *     });
 *
 *     EventManager::trigger('event name');
 * </code>
 *
 * @package  Bluz\Proxy
 * @author   Anton Shevchuk
 *
 * @method   static Instance getInstance()
 *
 * @method   static Instance attach($eventName, $callback, $priority = 1)
 * @see      Instance::attach()
 *
 * @method   static string|object trigger($event, $target = null, $params = null)
 * @see      Instance::trigger()
 */
final class EventManager
{
    use ProxyTrait;

    /**
     * Init instance
     *
     * @return Instance
     */
    private static function initInstance()
    {
        return new Instance();
    }
}
