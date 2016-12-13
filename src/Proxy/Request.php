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
use Bluz\Request\RequestFactory;
use Psr\Http\Message\UriInterface;
use Zend\Diactoros\ServerRequest as Instance;

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
 * @todo Proxy class should be clean
 *
 * @method   static Instance getInstance()
 *
 * @method   static UriInterface getUri()
 * @see      \Zend\Diactoros\RequestTrait::getUri()
 */
class Request
{
    use ProxyTrait;

    /**
     * @const string HTTP methods
     */
    const METHOD_OPTIONS = 'OPTIONS';
    const METHOD_GET = 'GET';
    const METHOD_HEAD = 'HEAD';
    const METHOD_PATCH = 'PATCH';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';
    const METHOD_TRACE = 'TRACE';
    const METHOD_CONNECT = 'CONNECT';

    /**
     * @const string HTTP content types
     */
    const TYPE_ANY = '*/*';
    const TYPE_HTML = 'text/html';
    const TYPE_JSON = 'application/json';

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
        return RequestFactory::get($key, self::getInstance()->getServerParams(), $default);
    }

    /**
     * Retrieve a member of the $_COOKIE super global
     *
     * If no $key is passed, returns the entire $_COOKIE array.
     *
     * @param  string $key
     * @param  string $default Default value to use if key not found
     * @return string Returns null if key does not exist
     */
    public static function getCookie($key = null, $default = null)
    {
        return RequestFactory::get($key, self::getInstance()->getCookieParams(), $default);
    }
    /**
     * Retrieve a member of the $_ENV super global
     *
     * If no $key is passed, returns the entire $_ENV array.
     *
     * @param  string $key
     * @param  string $default Default value to use if key not found
     * @return string Returns null if key does not exist
     */
    public static function getEnv($key = null, $default = null)
    {
        return RequestFactory::get($key, $_ENV, $default);
    }

    /**
     * Search for a header value
     *
     * @param string $header
     * @param mixed  $default
     * @return string
     */
    public static function getHeader($header, $default = null)
    {
        return RequestFactory::getHeader($header, self::getInstance()->getHeaders(), $default);
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
     * Get uploaded file
     *
     * @param  string $name
     * @return \Zend\Diactoros\UploadedFile
     */
    public static function getFile($name)
    {
        return RequestFactory::get($name, self::getInstance()->getUploadedFiles());
    }

    /**
     * Get the client's IP address
     *
     * @param  bool $checkProxy
     * @return string
     */
    public static function getClientIp($checkProxy = true)
    {
        if ($checkProxy && self::getServer('HTTP_CLIENT_IP') != null) {
            $ip = self::getServer('HTTP_CLIENT_IP');
        } else {
            if ($checkProxy && self::getServer('HTTP_X_FORWARDED_FOR') != null) {
                $ip = self::getServer('HTTP_X_FORWARDED_FOR');
            } else {
                $ip = self::getServer('REMOTE_ADDR');
            }
        }
        return $ip;
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
     * Get method
     *
     * @return string
     */
    public static function getMethod()
    {
        return self::getParam('_method', self::getInstance()->getMethod());
    }

    /**
     * Get Accept MIME Type
     *
     * @todo:  refactoring this method, accept types should be stored in static? variable
     * @param  array $allowTypes
     * @return string
     */
    public static function getAccept($allowTypes = [])
    {
        static $accept;

        if (!$accept) {
            // get header from request
            $header = self::getHeader('accept');

            // make array if types
            $header = explode(',', $header);
            $header = array_map('trim', $header);

            // result store
            $types = [];

            foreach ($header as $a) {
                // the default quality is 1.
                $q = 1;
                // check if there is a different quality
                if (strpos($a, ';q=') or strpos($a, '; q=')) {
                    // divide "mime/type;q=X" into two parts: "mime/type" i "X"
                    $res = preg_split('/;([ ]?)q=/', $a);
                    $a = $res[0];
                    $q = $res[1];
                }
                // remove other extension
                if (strpos($a, ';')) {
                    $a = substr($a, 0, strpos($a, ';'));
                }

                // mime-type $a is accepted with the quality $q
                // WARNING: $q == 0 means, that mime-type isn’t supported!
                $types[$a] = (float) $q;
            }
            arsort($types);
            $accept = $types;
        }

        // if no parameter was passed, just return parsed data
        if (empty($allowTypes)) {
            return $accept;
        }

        $mimeTypes = array_map('strtolower', $allowTypes);

        // let’s check our supported types:
        foreach ($accept as $mime => $q) {
            if ($q && in_array($mime, $mimeTypes)) {
                return $mime;
            }
        }
        // no mime-type found
        return null;
    }
    
    /**
     * Check CLI
     *
     * @return bool
     */
    public static function isCli()
    {
        return (PHP_SAPI === 'cli');
    }

    /**
     * Check HTTP
     *
     * @return bool
     */
    public static function isHttp()
    {
        return (PHP_SAPI !== 'cli');
    }

    /**
     * Is this a GET method request?
     *
     * @return bool
     */
    public static function isGet()
    {
        return (self::getInstance()->getMethod() === 'GET');
    }

    /**
     * Is this a POST method request?
     *
     * @return bool
     */
    public static function isPost()
    {
        return (self::getInstance()->getMethod() === 'POST');
    }

    /**
     * Is this a PUT method request?
     *
     * @return bool
     */
    public static function isPut()
    {
        return (self::getInstance()->getMethod() === 'PUT');
    }

    /**
     * Is this a DELETE method request?
     *
     * @return bool
     */
    public static function isDelete()
    {
        return (self::getInstance()->getMethod() === 'DELETE');
    }

    /**
     * Is the request a Javascript XMLHttpRequest?
     *
     * @return bool
     */
    public static function isXmlHttpRequest()
    {
        return (self::getHeader('X-Requested-With') == 'XMLHttpRequest');
    }
}
