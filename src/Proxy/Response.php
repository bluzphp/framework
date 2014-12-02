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

use Bluz\Common\Exception\ComponentException;
use Bluz\Response\AbstractResponse as Instance;
use Bluz\View\View;

/**
 * Proxy to Response
 *
 * Example of usage
 *     use Bluz\Proxy\Response;
 *
 *     Response::setStatusCode(304);
 *     Response::setHeader('Location', '/index/index');
 *
 * @package  Bluz\Proxy
 *
 * @method   static Instance getInstance()
 *
 * @method   static void send()
 * @see      Bluz\Response\AbstractResponse::send()
 *
 * @method   static string getProtocolVersion()
 * @see      Bluz\Response\AbstractResponse::getProtocolVersion()
 *
 * @method   static string getStatusCode()
 * @see      Bluz\Response\AbstractResponse::getStatusCode()
 * @method   static void  setStatusCode($code)
 * @see      Bluz\Response\AbstractResponse::setStatusCode()
 *
 * @method   static void  setReasonPhrase($phrase)
 * @see      Bluz\Response\AbstractResponse::setReasonPhrase()
 * @method   static string getReasonPhrase()
 * @see      Bluz\Response\AbstractResponse::getReasonPhrase()
 *
 * @method   static string getHeader($header)
 * @see      Bluz\Response\AbstractResponse::getHeader()
 * @method   static array  getHeaderAsArray($header)
 * @see      Bluz\Response\AbstractResponse::getHeaderAsArray()
 * @method   static bool   hasHeader($header)
 * @see      Bluz\Response\AbstractResponse::hasHeader()
 * @method   static void   setHeader($header, $value)
 * @see      Bluz\Response\AbstractResponse::setHeader()
 * @method   static void   addHeader($header, $value)
 * @see      Bluz\Response\AbstractResponse::addHeader()
 * @method   static void   removeHeader($header)
 * @see      Bluz\Response\AbstractResponse::removeHeader()
 *
 * @method   static array  getHeaders()
 * @see      Bluz\Response\AbstractResponse::getHeaders()
 * @method   static void   setHeaders(array $headers)
 * @see      Bluz\Response\AbstractResponse::setHeaders()
 * @method   static void   addHeaders(array $headers)
 * @see      Bluz\Response\AbstractResponse::addHeaders()
 * @method   static void   removeHeaders()
 * @see      Bluz\Response\AbstractResponse::removeHeaders()
 *
 * @method   static void  setBody($phrase)
 * @see      Bluz\Response\AbstractResponse::setBody()
 * @method   static View  getBody()
 * @see      Bluz\Response\AbstractResponse::getBody()
 * @method   static void  clearBody()
 * @see      Bluz\Response\AbstractResponse::clearBody()
 *
 * @method   static void  setCookie()
 * @see      Bluz\Response\AbstractResponse::setCookie($name, $value = null, $expire = 0, $path = '/', $domain = null,
 *              $secure = null, $httpOnly = null)
 * @method   static array getCookie()
 * @see      Bluz\Response\AbstractResponse::getCookie()
 *
 * @method   static void  setException($exception)
 * @see      Bluz\Response\AbstractResponse::setException()
 * @method   static \Exception getException()
 * @see      Bluz\Response\AbstractResponse::getException()
 *
 * @method   static void  setPresentation($presentation)
 * @see      Bluz\Response\AbstractResponse::setPresentation()
 * @method   static \Exception getPresentation()
 * @see      Bluz\Response\AbstractResponse::getPresentation()
 *
 * @author   Anton Shevchuk
 * @created  29.09.2014 12:15
 */
class Response extends AbstractProxy
{
    /**
     * Init instance
     *
     * @throws ComponentException
     * @return Instance
     */
    protected static function initInstance()
    {
        throw new ComponentException("Class `Proxy\\Request` required external initialization");
    }
}
