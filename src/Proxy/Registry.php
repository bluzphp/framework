<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Proxy;

use Bluz\Registry\Registry as Instance;

/**
 * Proxy to Registry
 *
 * Example of usage
 * <code>
 *     use Bluz\Proxy\Registry;
 *
 *     Registry::set('key', 'value');
 *     Registry::get('key');
 * </code>
 *
 * @package  Bluz\Proxy
 * @author   Anton Shevchuk
 *
 * @method   static Instance getInstance()
 *
 * @method   static void  set($key, $value)
 * @see      Instance::set()
 *
 * @method   static mixed get($key)
 * @see      Instance::get()
 *
 * @method   static bool  contains($key)
 * @see      Instance::contains()
 *
 * @method   static void  delete($key)
 * @see      Instance::delete()
 */
final class Registry
{
    use ProxyTrait;

    /**
     * Init instance
     *
     * @return Instance
     */
    private static function initInstance() : Instance
    {
        $instance = new Instance();
        if ($data = Config::getData('registry')) {
            $instance->setFromArray($data);
        }
        return $instance;
    }
}
