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

use Bluz\Http\CacheControl as Instance;
use Bluz\Common\Nil;

/**
 * Proxy to Http\CacheControl
 *
 * Example of usage
 *     use Bluz\Proxy\HttpCacheControl;
 *
 *
 * @package  Bluz\Proxy
 *
 * @method   static Instance getInstance()
 *
 * @method   static void setPrivate()
 * @see      Bluz\Http\CacheControl::setPrivate()
 *
 * @method   static void setPublic()
 * @see      Bluz\Http\CacheControl::setPublic()
 *
 * @method   static int getMaxAge()
 * @see      Bluz\Http\CacheControl::getMaxAge()
 *
 * @method   static void setMaxAge($value)
 * @see      Bluz\Http\CacheControl::getMaxAge()
 *
 * @method   static void setSharedMaxAge($value)
 * @see      Bluz\Http\CacheControl::getMaxAge()
 *
 * @method   static int getTtl()
 * @see      Bluz\Http\CacheControl::getTtl()
 *
 * @method   static void setTtl($seconds)
 * @see      Bluz\Http\CacheControl::setTtl()
 *
 * @method   static void setClientTtl($seconds)
 * @see      Bluz\Http\CacheControl::setClientTtl()
 *
 * @method   static string getEtag()
 * @see      Bluz\Http\CacheControl::getEtag()
 *
 * @method   static void setEtag($etag, $weak = false)
 * @see      Bluz\Http\CacheControl::setEtag()
 *
 * @method   static int getAge()
 * @see      Bluz\Http\CacheControl::getAge()
 *
 * @method   static void setAge($age)
 * @see      Bluz\Http\CacheControl::setAge()
 *
 * @method   static \DateTime getExpires()
 * @see      Bluz\Http\CacheControl::getExpires()
 *
 * @method   static void setExpires($date)
 * @see      Bluz\Http\CacheControl::setExpires()
 *
 * @method   static \DateTime|null getLastModified()
 * @see      Bluz\Http\CacheControl::getLastModified()
 *
 * @method   static void setLastModified($date)
 * @see      Bluz\Http\CacheControl::setLastModified()
 *
 * @author   Anton Shevchuk
 * @created  06.11.2014 13:21
 */
class HttpCacheControl extends AbstractProxy
{
    /**
     * Init instance
     *
     * @return Instance
     */
    protected static function initInstance()
    {
        if ('cli' === PHP_SAPI) {
            return new Nil();
        } else {
            return new Instance(Response::getInstance());
        }
    }
}
