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
 * <code>
 *     use Bluz\Proxy\Request;
 *
 *     Request::getParam('foo');
 * </code>
 *
 * @package  Bluz\Proxy
 * @author   Anton Shevchuk
 *
 * @method   static Instance getInstance()
 *
 * @method   static string getMethod()
 * @see      Bluz\Request\AbstractRequest::getMethod()
 * @method   static void   setMethod($method)
 * @see      Bluz\Request\AbstractRequest::setMethod()
 *
 * @method   static string getRequestUri()
 * @see      Bluz\Request\AbstractRequest::getRequestUri()
 * @method   static void   setRequestUri($requestUri)
 * @see      Bluz\Request\AbstractRequest::setRequestUri()
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
 * @method   static string getAccept()
 * @see      Bluz\Http\Request::getAccept()
 * @method   static string getHeader($header)
 * @see      Bluz\Http\Request::getHeader()
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
     */
    protected static function initInstance()
    {
        throw new ComponentException("Class `Proxy\\Request` required external initialization");
    }
    
    /**
     * getRequestUri
     * 
     * @return string
     */
    public static function getRequestUri()
    {
        return self::getInstance()->getUri();
    }

    /**
     * Retrieve a member of the $_SERVER super global
     *
     * If no $key is passed, returns the entire $_SERVER array.
     *
     * @param  string $key
     * @param  string $default Default value to use if key not found
     * @return string Returns null if key does not exist
     */
    public static function getServer($key = null, $default = null)
    {
        $params = self::getInstance()->getServerParams();
        if (null === $key) {
            return $params;
        }
        return (isset($params[$key])) ? $params[$key] : $default;
    }

    /**
     * Access values contained in the superglobals as public members
     * Order of precedence: 1. GET, 2. POST, 3. COOKIE, 4. SERVER, 5. ENV
     *
     * @param  string $key
     * @param  null   $default
     * @return mixed
     * @link http://msdn.microsoft.com/en-us/library/system.web.httprequest.item.aspx
     */
    public static function getParam($key, $default = null)
    {
        switch (true) {
            case ($params = self::getInstance()->getQueryParams()) && isset($params[$key]):
                return $params[$key];
            case ($params = self::getInstance()->getParsedBody()) && isset($params[$key]):
                return $params[$key];
            case ($params = self::getInstance()->getCookieParams()) && isset($params[$key]):
                return $params[$key];
            case ($params = self::getInstance()->getServerParams()) && isset($params[$key]):
                return $params[$key];
            case isset($_ENV[$key]):
                return $_ENV[$key];
            default:
                return $default;
        }
    }

    /**
     * Get all params from GET and POST or PUT
     *
     * @return array
     */
    public static function getParams()
    {
        $query = (array) self::getInstance()->getQueryParams();
        $body = (array) self::getInstance()->getParsedBody();

        return array_merge($body, $query);
    }

    /**
     * Get module
     *
     * @return string
     */
    public static function getModule()
    {
        return self::getParam('_module', Router::getDefaultModule());
    }

    /**
     * Get controller
     *
     * @return string
     */
    public static function getController()
    {
        return self::getParam('_controller', Router::getDefaultController());
    }

    /**
     * Check CLI
     *
     * @return bool
     */
    public static function isCli()
    {
        return (self::getInstance()->getMethod() == self::METHOD_CLI);
    }

    /**
     * Check HTTP
     *
     * @return bool
     */
    public static function isHttp()
    {
        return (self::getInstance()->getMethod() != self::METHOD_CLI);
    }

    /**
     * Is this a GET method request?
     *
     * @return bool
     */
    public static function isGet()
    {
        return (self::getInstance()->getMethod() === self::METHOD_GET);
    }

    /**
     * Is this a POST method request?
     *
     * @return bool
     */
    public static function isPost()
    {
        return (self::getInstance()->getMethod() === self::METHOD_POST);
    }

    /**
     * Is this a PUT method request?
     *
     * @return bool
     */
    public static function isPut()
    {
        return (self::getInstance()->getMethod() === self::METHOD_PUT);
    }

    /**
     * Is this a DELETE method request?
     *
     * @return bool
     */
    public static function isDelete()
    {
        return (self::getInstance()->getMethod() === self::METHOD_DELETE);
    }

    /**
     * Is the request a Javascript XMLHttpRequest?
     *
     * Should work with Prototype/Script.aculo.us, possibly others.
     *
     * @return bool
     */
    public static function isXmlHttpRequest()
    {
        return (current(self::getInstance()->getHeader('X-Requested-With')) == 'XMLHttpRequest');
    }

    /**
     * Get Accept MIME Type
     * @return string
     */
    public static function getAccept($allowTypes = [])
    {
        // get header from request
        $header = self::getInstance()->getHeader('accept');
        $header = current($header);
        // make array if types
        $accept = explode(',', $header);
        $accept = array_map('trim', $accept);

        // result store
        $types = [];

        foreach ($accept as $a) {
            // the default quality is 1.
            $q = 1;
            // check if there is a different quality
            if (strpos($a, ';q=') or strpos($a, '; q=')) {
                // divide "mime/type;q=X" into two parts: "mime/type" i "X"
                $res = preg_split('/;([ ]?)q=/', $a);
                $a = $res[0];
                $q = $res[1];
            }
            // mime-type $a is accepted with the quality $q
            // WARNING: $q == 0 means, that mime-type isn’t supported!
            $types[$a] = (float) $q;
        }
        arsort($types);

        // if no parameter was passed, just return parsed data
        if (empty($allowTypes)) {
            return $types;
        }

        $mimeTypes = array_map('strtolower', $allowTypes);

        // let’s check our supported types:
        foreach ($types as $mime => $q) {
            if ($q && in_array($mime, $mimeTypes)) {
                return $mime;
            }
        }
        // no mime-type found
        return null;
    }
}
