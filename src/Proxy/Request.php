<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Proxy;

use Bluz\Common\Exception\ComponentException;
use Bluz\Http\RequestMethod;
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
     * Retrieve a member of the $_GET super global
     *
     * If no $key is passed, returns the entire $_GET array.
     *
     * @param  string $key
     * @param  string $default Default value to use if key not found
     * @return string Returns null if key does not exist
     */
    public static function getQuery($key = null, $default = null)
    {
        return RequestFactory::get($key, self::getInstance()->getQueryParams(), $default);
    }

    /**
     * Retrieve a member of the $_POST super global
     *
     * If no $key is passed, returns the entire $_POST array.
     *
     * @param  string $key
     * @param  string $default Default value to use if key not found
     * @return string Returns null if key does not exist
     */
    public static function getPost($key = null, $default = null)
    {
        return RequestFactory::get($key, (array)self::getInstance()->getParsedBody(), $default);
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
     * Order of precedence: 1. GET, 2. POST, 3. COOKIE, 4. SERVER
     *
     * @param  string $key
     * @param  null   $default
     * @return string|null
     * @link http://msdn.microsoft.com/en-us/library/system.web.httprequest.item.aspx
     */
    public static function getParam($key, $default = null)
    {
        return
            self::getQuery($key) ??
            self::getPost($key) ??
            self::getCookie($key) ??
            self::getServer($key) ??
            $default;
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
            return self::getServer('HTTP_CLIENT_IP');
        } elseif ($checkProxy && self::getServer('HTTP_X_FORWARDED_FOR') != null) {
            return self::getServer('HTTP_X_FORWARDED_FOR');
        } else {
            return self::getServer('REMOTE_ADDR');
        }
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

            // nothing ...
            if (!$header) {
                return null;
            }

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
        return (self::getInstance()->getMethod() === RequestMethod::GET);
    }

    /**
     * Is this a POST method request?
     *
     * @return bool
     */
    public static function isPost()
    {
        return (self::getInstance()->getMethod() === RequestMethod::POST);
    }

    /**
     * Is this a PUT method request?
     *
     * @return bool
     */
    public static function isPut()
    {
        return (self::getInstance()->getMethod() === RequestMethod::PUT);
    }

    /**
     * Is this a DELETE method request?
     *
     * @return bool
     */
    public static function isDelete()
    {
        return (self::getInstance()->getMethod() === RequestMethod::DELETE);
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
