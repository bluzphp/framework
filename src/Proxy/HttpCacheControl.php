<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Proxy;

use Bluz\Common\Nil;
use Bluz\Http\CacheControl as Instance;

/**
 * Proxy to Http\CacheControl
 *
 * Example of usage
 * <code>
 *     use Bluz\Proxy\HttpCacheControl;
 * </code>
 *
 * @package  Bluz\Proxy
 * @author   Anton Shevchuk
 *
 * @method   static Instance getInstance()
 *
 * @method   static void setPrivate()
 * @see      Instance::setPrivate()
 *
 * @method   static void setPublic()
 * @see      Instance::setPublic()
 *
 * @method   static integer getMaxAge()
 * @see      Instance::getMaxAge()
 *
 * @method   static void setMaxAge($value)
 * @see      Instance::getMaxAge()
 *
 * @method   static void setSharedMaxAge($value)
 * @see      Instance::getMaxAge()
 *
 * @method   static integer getTtl()
 * @see      Instance::getTtl()
 *
 * @method   static void setTtl($seconds)
 * @see      Instance::setTtl()
 *
 * @method   static void setClientTtl($seconds)
 * @see      Instance::setClientTtl()
 *
 * @method   static string getEtag()
 * @see      Instance::getEtag()
 *
 * @method   static void setEtag($etag, $weak = false)
 * @see      Instance::setEtag()
 *
 * @method   static integer getAge()
 * @see      Instance::getAge()
 *
 * @method   static void setAge($age)
 * @see      Instance::setAge()
 *
 * @method   static \DateTime getExpires()
 * @see      Instance::getExpires()
 *
 * @method   static void setExpires($date)
 * @see      Instance::setExpires()
 *
 * @method   static \DateTime|null getLastModified()
 * @see      Instance::getLastModified()
 *
 * @method   static void setLastModified($date)
 * @see      Instance::setLastModified()
 */
class HttpCacheControl
{
    use ProxyTrait;

    /**
     * Init instance
     *
     * @return Instance|Nil
     */
    protected static function initInstance()
    {
        if (PHP_SAPI === 'cli') {
            return new Nil();
        }
        return new Instance(Response::getInstance());
    }
}
