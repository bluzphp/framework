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
 * @package  Bluz\Proxy
 *
 * @method   static Instance getInstance()
 *
 * @method   static void  set($key, $value)
 * @method   static mixed get($key)
 * @method   static bool  contains($key)
 * @method   static void  delete($key)
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
        $instance->setData(Config::getData('registry'));
        return $instance;
    }
}
