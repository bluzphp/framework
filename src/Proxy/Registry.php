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

use Bluz\Registry\Registry as Instance;

/**
 * Proxy to Registry
 *
 * Example of usage
 *     use Bluz\Proxy\Registry;
 *
 *     Registry::set('key', 'value');
 *     Registry::get('key');
 *
 * @package  Bluz\Proxy
 *
 * @method   static Instance getInstance()
 *
 * @method   static void  set($key, $value)
 * @see      Bluz\Registry\Registry::set()
 *
 * @method   static mixed get($key)
 * @see      Bluz\Registry\Registry::get()
 *
 * @method   static bool  contains($key)
 * @see      Bluz\Registry\Registry::contains()
 *
 * @method   static void  delete($key)
 * @see      Bluz\Registry\Registry::delete()
 *
 * @author   Anton Shevchuk
 * @created  29.09.2014 11:32
 */
class Registry extends AbstractProxy
{
    /**
     * Init instance
     *
     * @return Instance
     */
    protected static function initInstance()
    {
        $instance = new Instance();
        if ($data = Config::getData('registry')) {
            $instance->setFromArray($data);
        }
        return $instance;
    }
}
