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
use Bluz\Http\FileUpload;
use Bluz\Request\AbstractRequest as Instance;

/**
 * Proxy to Request
 *
 * Example of usage
 *     use Bluz\Proxy\Request;
 *
 *     Request::getParam('foo');
 *
 * @package  Bluz\Proxy
 *
 * @method   static Instance getInstance()
 *
 * @method   static string getMethod()
 * @see      Bluz\Request\AbstractRequest::getMethod()
 * @method   static void   setMethod($method)
 * @see      Bluz\Request\AbstractRequest::setMethod()
 *
 * @method   static string getBaseUrl()
 * @see      Bluz\Cli\Request::getBaseUrl()
 * @see      Bluz\Http\Request::getBaseUrl()
 * @method   static void   setBaseUrl($baseUrl)
 * @see      Bluz\Request\AbstractRequest::setBaseUrl()
 *
 * @method   static string getRequestUri()
 * @see      Bluz\Request\AbstractRequest::getRequestUri()
 * @method   static void   setRequestUri($requestUri)
 * @see      Bluz\Request\AbstractRequest::setRequestUri()
 *
 * @method   static string getCleanUri()
 * @see      Bluz\Request\AbstractRequest::getCleanUri()
 *
 * @method   static mixed getParam($key, $default = null)
 * @see      Bluz\Request\AbstractRequest::getParam()
 * @method   static void  setParam($key, $value)
 * @see      Bluz\Request\AbstractRequest::setParam()
 * @method   static array getParams()
 * @see      Bluz\Request\AbstractRequest::getParams()
 * @method   static array getAllParams()
 * @see      Bluz\Request\AbstractRequest::getAllParams()
 * @method   static void  setParams(array $params)
 * @see      Bluz\Request\AbstractRequest::setParams()
 * @method   static array getRawParams()
 * @see      Bluz\Request\AbstractRequest::getRawParams()
 * @method   static void  setRawParams(array $params)
 * @see      Bluz\Request\AbstractRequest::setRawParams()
 *
 * @method   static bool isCli()
 * @see      Bluz\Request\AbstractRequest::isCli()
 * @method   static bool isHttp()
 * @see      Bluz\Request\AbstractRequest::isHttp()
 * @method   static bool isGet()
 * @see      Bluz\Request\AbstractRequest::isGet()
 * @method   static bool isPost()
 * @see      Bluz\Request\AbstractRequest::isPost()
 * @method   static bool isPut()
 * @see      Bluz\Request\AbstractRequest::isPut()
 * @method   static bool isDelete()
 * @see      Bluz\Request\AbstractRequest::isDelete()
 *
 * @method   static string getServer($key = null, $default = null)
 * @see      Bluz\Request\AbstractRequest::getServer()
 * @method   static string getEnv($key = null, $default = null)
 * @see      Bluz\Request\AbstractRequest::getEnv()
 *
 * @method   static string getModule()
 * @see      Bluz\Request\AbstractRequest::getModule()
 * @method   static void   setModule($name)
 * @see      Bluz\Request\AbstractRequest::setModule()
 *
 * @method   static string getController()
 * @see      Bluz\Request\AbstractRequest::getController()
 * @method   static void   setController($name)
 * @see      Bluz\Request\AbstractRequest::setController()
 *
 * @method   static string|array getQuery($key = null, $default = null)
 * @see      Bluz\Http\Request::getQuery()
 * @method   static string|array getPost($key = null, $default = null)
 * @see      Bluz\Http\Request::getPost()
 * @method   static string|array getCookie($key = null, $default = null)
 * @see      Bluz\Http\Request::getCookie()
 *
 * @method   static string getHttpHost()
 * @see      Bluz\Http\Request::getHttpHost()
 * @method   static string getScheme()
 * @see      Bluz\Http\Request::getScheme()
 *
 * @method   static FileUpload getFileUpload()
 * @see      Bluz\Http\Request::getFileUpload()
 * @method   static void setFileUpload(FileUpload $fileUpload)
 * @see      Bluz\Http\Request::setFileUpload()
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
     * @const string HTTP METHOD constant names
     */
    const METHOD_OPTIONS = Instance::METHOD_OPTIONS;
    const METHOD_GET = Instance::METHOD_GET;
    const METHOD_HEAD = Instance::METHOD_HEAD;
    const METHOD_PATCH = Instance::METHOD_PATCH;
    const METHOD_POST = Instance::METHOD_POST;
    const METHOD_PUT = Instance::METHOD_PUT;
    const METHOD_DELETE = Instance::METHOD_DELETE;
    const METHOD_TRACE = Instance::METHOD_TRACE;
    const METHOD_CONNECT = Instance::METHOD_CONNECT;
    /**
     * Command line request
     */
    const METHOD_CLI = Instance::METHOD_CLI;

    /**
     * HTTP Request
     */
    const METHOD_HTTP = Instance::METHOD_HTTP;
    
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
