<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Proxy;

use Bluz\Common\Exception\ComponentException;
use Bluz\Controller\Controller;
use Bluz\Http\Exception\RedirectException;
use Bluz\Response\Response as Instance;

/**
 * Proxy to Response
 *
 * Example of usage
 * <code>
 *     use Bluz\Proxy\Response;
 *
 *     Response::setStatusCode(304);
 *     Response::setHeader('Location', '/index/index');
 * </code>
 *
 * @package  Bluz\Proxy
 * @author   Anton Shevchuk
 *
 * @method   static Instance getInstance()
 *
 * @method   static string getProtocolVersion()
 * @see      Instance::getProtocolVersion()
 *
 * @method   static string getStatusCode()
 * @see      Instance::getStatusCode()
 * @method   static void  setStatusCode($code)
 * @see      Instance::setStatusCode()
 *
 * @method   static void  setReasonPhrase($phrase)
 * @see      Instance::setReasonPhrase()
 * @method   static string getReasonPhrase()
 * @see      Instance::getReasonPhrase()
 *
 * @method   static string getHeader($header)
 * @see      Instance::getHeader()
 * @method   static array  getHeaderAsArray($header)
 * @see      Instance::getHeaderAsArray()
 * @method   static bool   hasHeader($header)
 * @see      Instance::hasHeader()
 * @method   static void   setHeader($header, $value)
 * @see      Instance::setHeader()
 * @method   static void   addHeader($header, $value)
 * @see      Instance::addHeader()
 * @method   static void   removeHeader($header)
 * @see      Instance::removeHeader()
 *
 * @method   static array  getHeaders()
 * @see      Instance::getHeaders()
 * @method   static void   setHeaders(array $headers)
 * @see      Instance::setHeaders()
 * @method   static void   addHeaders(array $headers)
 * @see      Instance::addHeaders()
 * @method   static void   removeHeaders()
 * @see      Instance::removeHeaders()
 *
 * @method   static void  setBody($phrase)
 * @see      Instance::setBody()
 * @method   static Controller  getBody()
 * @see      Instance::getBody()
 * @method   static void  clearBody()
 * @see      Instance::clearBody()
 *
 * @method   static void setCookie($name, $value = '', $expire = 0, $path = '/', $domain = '', $s = null, $h = null)
 * @see      Instance::setCookie()
 * @method   static array getCookie()
 * @see      Instance::getCookie()
 *
 * @method   static string getType()
 * @see      Instance::getType()
 * @method   static void setType($type)
 * @see      Instance::setType()
 *
 * @method   static void  send()
 * @see      Instance::send()
 */
final class Response
{
    use ProxyTrait;

    /**
     * Init instance
     *
     * @throws ComponentException
     */
    private static function initInstance()
    {
        throw new ComponentException("Class `Proxy\\Request` required external initialization");
    }

    /**
     * Redirect to URL
     *
     * @param  string $url
     *
     * @return void
     * @throws RedirectException
     */
    public static function redirect($url): void
    {
        $redirect = new RedirectException();
        $redirect->setUrl($url);
        throw $redirect;
    }

    /**
     * Redirect to controller
     *
     * @param  string $module
     * @param  string $controller
     * @param  array  $params
     *
     * @return void
     * @throws RedirectException
     */
    public static function redirectTo($module = 'index', $controller = 'index', array $params = []): void
    {
        $url = Router::getFullUrl($module, $controller, $params);
        self::redirect($url);
    }

    /**
     * Reload current page please, be careful to avoid loop of reload
     *
     * @return void
     * @throws RedirectException
     */
    public static function reload(): void
    {
        self::redirect((string) Request::getUri());
    }
}
