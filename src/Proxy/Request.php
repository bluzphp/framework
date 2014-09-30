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

use Bluz\Request\AbstractRequest as Instance;

/**
 * Proxy to Request
 *
 * @package  Bluz\Proxy
 *
 * @method   static Instance getInstance()
 *
 * @method   static void  setMethod($method)
 * @see      Bluz\Request\AbstractRequest::setMethod()
 * @method   static string getMethod()
 * @see      Bluz\Request\AbstractRequest::getMethod()
 *
 * @method   static void  setRequestUri($requestUri)
 * @see      Bluz\Request\AbstractRequest::setRequestUri()
 * @method   static string getRequestUri()
 * @see      Bluz\Request\AbstractRequest::getRequestUri()
 *
 * @method   static void  setModule($name)
 * @see      Bluz\Request\AbstractRequest::setModule()
 * @method   static string getModule()
 * @see      Bluz\Request\AbstractRequest::getModule()
 *
 * @method   static void  setController($name)
 * @see      Bluz\Request\AbstractRequest::setController()
 * @method   static string getController()
 * @see      Bluz\Request\AbstractRequest::getController()
 *
 * @method   static void  setParam($key, $value)
 * @see      Bluz\Request\AbstractRequest::setParam()
 * @method   static mixed getParam($key, $default = null)
 * @see      Bluz\Request\AbstractRequest::getParam()
 * @method   static void  setParams(array $params)
 * @see      Bluz\Request\AbstractRequest::setParams()
 * @method   static array getParams()
 * @see      Bluz\Request\AbstractRequest::getParams()
 * @method   static array getAllParams()
 * @see      Bluz\Request\AbstractRequest::getAllParams()
 *
 * @method   static bool isCli()
 * @see      Bluz\Http\Request::isCli()
 * @method   static bool isHttp()
 * @see      Bluz\Http\Request::isHttp()
 *
 * @method   static bool isGet()
 * @see      Bluz\Http\Request::isGet()
 * @method   static bool isPost()
 * @see      Bluz\Http\Request::isPost()
 * @method   static bool isPut()
 * @see      Bluz\Http\Request::isPut()
 * @method   static bool isDelete()
 * @see      Bluz\Http\Request::isDelete()
 *
 * @method   static bool isXmlHttpRequest()
 * @see      Bluz\Http\Request::isXmlHttpRequest()
 * @method   static bool isFlashRequest()
 * @see      Bluz\Http\Request::isFlashRequest()
 *
 * @author   Anton Shevchuk
 * @created  29.09.2014 12:15
 */
class Request extends AbstractProxy
{
    /**
     * Init instance
     *
     * @return Instance
     */
    protected static function initInstance()
    {
        return null;
    }
}
