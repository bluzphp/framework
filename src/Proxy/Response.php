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

use Bluz\Application\Exception\RedirectException;
use Bluz\Application\Exception\ReloadException;
use Bluz\Common\Exception\ComponentException;
use Bluz\Controller\Controller;
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
 * @see      Bluz\Response\Response::getProtocolVersion()
 *
 * @method   static string getStatusCode()
 * @see      Bluz\Response\Response::getStatusCode()
 * @method   static void  setStatusCode($code)
 * @see      Bluz\Response\Response::setStatusCode()
 *
 * @method   static void  setReasonPhrase($phrase)
 * @see      Bluz\Response\Response::setReasonPhrase()
 * @method   static string getReasonPhrase()
 * @see      Bluz\Response\Response::getReasonPhrase()
 *
 * @method   static string getHeader($header)
 * @see      Bluz\Response\Response::getHeader()
 * @method   static array  getHeaderAsArray($header)
 * @see      Bluz\Response\Response::getHeaderAsArray()
 * @method   static bool   hasHeader($header)
 * @see      Bluz\Response\Response::hasHeader()
 * @method   static void   setHeader($header, $value)
 * @see      Bluz\Response\Response::setHeader()
 * @method   static void   addHeader($header, $value)
 * @see      Bluz\Response\Response::addHeader()
 * @method   static void   removeHeader($header)
 * @see      Bluz\Response\Response::removeHeader()
 *
 * @method   static array  getHeaders()
 * @see      Bluz\Response\Response::getHeaders()
 * @method   static void   setHeaders(array $headers)
 * @see      Bluz\Response\Response::setHeaders()
 * @method   static void   addHeaders(array $headers)
 * @see      Bluz\Response\Response::addHeaders()
 * @method   static void   removeHeaders()
 * @see      Bluz\Response\Response::removeHeaders()
 *
 * @method   static void  setBody($phrase)
 * @see      Bluz\Response\Response::setBody()
 * @method   static Controller  getBody()
 * @see      Bluz\Response\Response::getBody()
 * @method   static void  clearBody()
 * @see      Bluz\Response\Response::clearBody()
 *
 * @method   static void setCookie($name, $value = '', $expire = 0, $path = '/', $domain = '', $s = null, $h = null)
 * @see      Bluz\Response\Response::setCookie()
 * @method   static array getCookie()
 * @see      Bluz\Response\Response::getCookie()
 *
 * @method   static switchType($type)
 * @see      Bluz\Response\Response::switchType()
 
 * @method   static void  send()
 * @see      Bluz\Response\Response::send()
 */
class Response extends AbstractProxy
{
    /**
     * Init instance
     *
     * @throws ComponentException
     */
    protected static function initInstance()
    {
        throw new ComponentException("Class `Proxy\\Request` required external initialization");
    }

    /**
     * Redirect to URL
     *
     * @param  string $url
     * @return void
     * @throws RedirectException
     */
    public static function redirect($url)
    {
        $redirect = new RedirectException();
        $redirect->setUrl($url);
        throw $redirect;
    }

    /**
     * Redirect to controller
     *
     * @param  string      $module
     * @param  string      $controller
     * @param  array       $params
     * @return void
     */
    public static function redirectTo($module = 'index', $controller = 'index', $params = array())
    {
        $url = Router::getUrl($module, $controller, $params);
        self::redirect($url);
    }

    /**
     * Reload current page please, be careful to avoid loop of reload
     *
     * @return void
     * @throws ReloadException
     */
    public static function reload()
    {
        self::redirect(Request::getUri());
    }
}
