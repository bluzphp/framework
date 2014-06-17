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
namespace Bluz\Http;

use Bluz\Request\AbstractRequest;

/**
 * HTTP Request
 *
 * @package  Bluz\Http
 *
 * @author   Anton Shevchuk
 * @created  06.07.11 16:59
 */
class Request extends AbstractRequest
{
    /**
     * @const string HTTP METHOD constant names
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
     * @const string HTTP SCHEME constant names
     */
    const SCHEME_HTTP = 'http';
    const SCHEME_HTTPS = 'https';

    /**
     * File upload instance
     * @var FileUpload
     */
    protected $fileUpload;

    /**
     * Class handler downloadable files
     * @var string
     */
    protected $className = 'Bluz\Http\FileUpload';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->method = $this->getServer('REQUEST_METHOD');
        $request = file_get_contents('php://input');
        $contentType = $this->getHeader('Content-Type');

        // support header like "application/json" and "application/json; charset=utf-8"
        if ($contentType && stristr($contentType, 'application/json')) {
            $data = (array) json_decode($request);
        } else {
            switch ($this->method) {
                case self::METHOD_POST:
                    $data = $_POST;
                    break;
                default:
                    parse_str($request, $data);
                    break;
            }
        }

        $this->setParams($data);
    }

    /**
     * Access values contained in the superglobals as public members
     * Order of precedence: 1. GET, 2. POST, 3. COOKIE, 4. SERVER, 5. ENV
     *
     * @link http://msdn.microsoft.com/en-us/library/system.web.httprequest.item.aspx
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        switch (true) {
            case parent::__isset($key):
                return parent::__get($key);
            case isset($_GET[$key]):
                return $_GET[$key];
            case isset($_POST[$key]):
                return $_POST[$key];
            case isset($_COOKIE[$key]):
                return $_COOKIE[$key];
            case isset($_SERVER[$key]):
                return $_SERVER[$key];
            case isset($_ENV[$key]):
                return $_ENV[$key];
            default:
                return null;
        }
    }

    /**
     * Check to see if a property is set
     *
     * @param string $key
     * @return bool
     */
    public function __isset($key)
    {
        switch (true) {
            case parent::__isset($key):
                return true;
            case isset($_GET[$key]):
                return true;
            case isset($_POST[$key]):
                return true;
            case isset($_COOKIE[$key]):
                return true;
            case isset($_SERVER[$key]):
                return true;
            case isset($_ENV[$key]):
                return true;
            default:
                return false;
        }
    }

    /**
     * Unset custom param
     *
     * @param $key
     */
    public function __unset($key)
    {
        parent::__unset($key);

        if (isset($_GET[$key])) {
            unset($_GET[$key]);
        }
        if (isset($_POST[$key])) {
            unset($_POST[$key]);
        }
        if (isset($_COOKIE[$key])) {
            unset($_COOKIE[$key]);
        }
        if (isset($_SERVER[$key])) {
            unset($_SERVER[$key]);
        }
        if (isset($_ENV[$key])) {
            unset($_ENV[$key]);
        }
    }

    /**
     * Get all request parameters
     *
     * @return array
     */
    public function getAllParams()
    {
        return array_merge($_POST, $_GET, $this->params);
    }

    /**
     * Get the request URI scheme
     *
     * @return string
     */
    public function getScheme()
    {
        return ($this->getServer('HTTPS') == 'on') ? self::SCHEME_HTTPS : self::SCHEME_HTTP;
    }

    /**
     * Is this a GET method request?
     *
     * @return bool
     */
    public function isGet()
    {
        return ($this->getMethod() === self::METHOD_GET);
    }

    /**
     * Is this a POST method request?
     *
     * @return bool
     */
    public function isPost()
    {
        return ($this->getMethod() === self::METHOD_POST);
    }

    /**
     * Is this a PUT method request?
     *
     * @return bool
     */
    public function isPut()
    {
        return ($this->getMethod() === self::METHOD_PUT);
    }

    /**
     * Is this a DELETE method request?
     *
     * @return bool
     */
    public function isDelete()
    {
        return ($this->getMethod() === self::METHOD_DELETE);
    }

    /**
     * Is the request a Javascript XMLHttpRequest?
     *
     * Should work with Prototype/Script.aculo.us, possibly others.
     *
     * @return bool
     */
    public function isXmlHttpRequest()
    {
        return ($this->getHeader('X_REQUESTED_WITH') == 'XMLHttpRequest');
    }

    /**
     * Is this a Flash request?
     *
     * @return bool
     */
    public function isFlashRequest()
    {
        if ($header = $this->getHeader('USER_AGENT')) {
            return (strstr(strtolower($header), ' flash')) ? true : false;
        } else {
            return false;
        }
    }

    /**
     * Return the value of the given HTTP header. Pass the header name as the
     * plain, HTTP-specified header name. Ex.: Ask for 'Accept' to get the
     * Accept header, 'Accept-Encoding' to get the Accept-Encoding header.
     *
     * @param string $header HTTP header name
     * @return string|false HTTP header value, or false if not found
     */
    public function getHeader($header)
    {
        // Try to get it from the $_SERVER array first
        $temp = 'HTTP_' . strtoupper(str_replace('-', '_', $header));
        if (isset($_SERVER[$temp])) {
            return $_SERVER[$temp];
        }
        // This seems to be the only way to get the Authorization header on
        // Apache
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            if (isset($headers[$header])) {
                return $headers[$header];
            }
            $header = strtolower($header);
            foreach ($headers as $key => $value) {
                if (strtolower($key) == $header) {
                    return $value;
                }
            }
        }

        return false;
    }

    /**
     * Retrieve a member of the $_GET super global
     *
     * If no $key is passed, returns the entire $_GET array.
     *
     * @param string $key
     * @param mixed $default Default value to use if key not found
     * @return mixed Returns null if key does not exist
     */
    public function getQuery($key = null, $default = null)
    {
        if (null === $key) {
            return $_GET;
        }

        return (isset($_GET[$key])) ? $_GET[$key] : $default;
    }


    /**
     * Retrieve a member of the $_POST super global
     *
     * If no $key is passed, returns the entire $_POST array.
     *
     * @param string $key
     * @param mixed $default Default value to use if key not found
     * @return mixed Returns null if key does not exist
     */
    public function getPost($key = null, $default = null)
    {
        if (null === $key) {
            return $_POST;
        }

        return (isset($_POST[$key])) ? $_POST[$key] : $default;
    }

    /**
     * setUploadClassName
     *
     * @param string $className
     *
     * @return void
     */
    public function setUploadClassName($className)
    {
        $this->className = $className;
    }

    /**
     * getFileUpload
     *
     * @return FileUpload
     */
    public function getFileUpload()
    {
        if (!$this->fileUpload) {
            $this->fileUpload = new $this->className();
        }
        return $this->fileUpload;
    }

    /**
     * Retrieve a member of the $_COOKIE superglobal
     *
     * If no $key is passed, returns the entire $_COOKIE array.
     *
     * @todo How to retrieve from nested arrays
     * @param string $key
     * @param mixed $default Default value to use if key not found
     * @return mixed Returns null if key does not exist
     */
    public function getCookie($key = null, $default = null)
    {
        if (null === $key) {
            return $_COOKIE;
        }

        return (isset($_COOKIE[$key])) ? $_COOKIE[$key] : $default;
    }

    /**
     * Retrieve a member of the $_SERVER superglobal
     *
     * If no $key is passed, returns the entire $_SERVER array.
     *
     * @param string $key
     * @param string $default Default value to use if key not found
     * @return mixed Returns null if key does not exist
     */
    public function getServer($key = null, $default = null)
    {
        if (null === $key) {
            return $_SERVER;
        }

        return (isset($_SERVER[$key])) ? $_SERVER[$key] : $default;
    }

    /**
     * Get the HTTP host.
     *
     * "Host" ":" host [ ":" port ] ; Section 3.2.2
     * Note the HTTP Host header is not the same as the URI host.
     * It includes the port while the URI host doesn't.
     *
     * @return string
     */
    public function getHttpHost()
    {
        $host = $this->getServer('HTTP_HOST');
        if (!empty($host)) {
            return $host;
        }

        $scheme = $this->getScheme();
        $name = $this->getServer('SERVER_NAME');
        $port = $this->getServer('SERVER_PORT');

        if (null === $name) {
            return '';
        } elseif (($scheme == self::SCHEME_HTTP && $port == 80) || ($scheme == self::SCHEME_HTTPS && $port == 443)) {
            return $name;
        } else {
            return $name . ':' . $port;
        }
    }

    /**
     * Get the request URI.
     *
     * @return string
     */
    public function getRequestUri()
    {
        if ($this->requestUri === null) {
            $this->requestUri = $this->detectRequestUri();
        }
        return $this->requestUri;
    }

    /**
     * Get the base URL.
     *
     * @return string
     */
    public function getBaseUrl()
    {
        if (null === $this->baseUrl) {
            $this->setBaseUrl($this->detectBaseUrl());
        }

        return $this->baseUrl;
    }

    /**
     * Get the request URI without baseUrl
     *
     * @return string
     */
    public function getCleanUri()
    {
        if ($this->cleanUri === null) {
            $uri = parse_url($this->getRequestUri());
            $uri = $uri['path'];

            if ($this->getBaseUrl() && strpos($uri, $this->getBaseUrl()) === 0) {
                $uri = substr($uri, strlen($this->getBaseUrl()));
            }
            $this->cleanUri = $uri;
        }
        return $this->cleanUri;
    }


    /**
     * Get the client's IP address
     *
     * @param  bool $checkProxy
     * @return string
     */
    public function getClientIp($checkProxy = true)
    {
        if ($checkProxy && $this->getServer('HTTP_CLIENT_IP') != null) {
            $ip = $this->getServer('HTTP_CLIENT_IP');
        } else {
            if ($checkProxy && $this->getServer('HTTP_X_FORWARDED_FOR') != null) {
                $ip = $this->getServer('HTTP_X_FORWARDED_FOR');
            } else {
                $ip = $this->getServer('REMOTE_ADDR');
            }
        }

        return $ip;
    }

    /**
     * Detect the base URI for the request
     *
     * Looks at a variety of criteria in order to attempt to autodetect a base
     * URI, including rewrite URIs, proxy URIs, etc.
     *
     * @return string
     */
    protected function detectRequestUri()
    {
        $requestUri = null;

        // Check this first so IIS will catch.
        $httpXRewriteUrl = $this->getServer('HTTP_X_REWRITE_URL');
        if ($httpXRewriteUrl !== null) {
            $requestUri = $httpXRewriteUrl;
        }

        // IIS7 with URL Rewrite: make sure we get the unencoded url
        // (double slash problem).
        $iisUrlRewritten = $this->getServer('IIS_WasUrlRewritten');
        $unencodedUrl = $this->getServer('UNENCODED_URL', '');
        if ('1' == $iisUrlRewritten && '' !== $unencodedUrl) {
            return $unencodedUrl;
        }

        // HTTP proxy requests setup request URI with scheme and host
        // [and port] + the URL path, only use URL path.
        if (!$httpXRewriteUrl) {
            $requestUri = $this->getServer('REQUEST_URI');
        }
        if ($requestUri !== null) {
            $schemeAndHttpHost = $this->getScheme() . '://' . $this->getServer('HTTP_HOST');

            if (strpos($requestUri, $schemeAndHttpHost) === 0) {
                $requestUri = substr($requestUri, strlen($schemeAndHttpHost));
            }
            return $requestUri;
        }

        // IIS 5.0, PHP as CGI.
        $origPathInfo = $this->getServer('ORIG_PATH_INFO');
        if ($origPathInfo !== null) {
            $queryString = $this->getServer('QUERY_STRING', '');
            if ($queryString !== '') {
                $origPathInfo .= '?' . $queryString;
            }
            return $origPathInfo;
        }

        return '/';
    }

    /**
     * Auto-detect the base path from the request environment
     *
     * Uses a variety of criteria in order to detect the base URL of the request
     * (i.e., anything additional to the document root).
     *
     * The base URL includes the schema, host, and port, in addition to the path.
     *
     * @return string
     */
    protected function detectBaseUrl()
    {
        $filename = $this->getServer('SCRIPT_FILENAME', '');
        $scriptName = $this->getServer('SCRIPT_NAME');
        $phpSelf = $this->getServer('PHP_SELF');
        $origScriptName = $this->getServer('ORIG_SCRIPT_NAME');

        if ($scriptName !== null && basename($scriptName) === $filename) {
            $baseUrl = $scriptName;
        } elseif ($phpSelf !== null && basename($phpSelf) === $filename) {
            $baseUrl = $phpSelf;
        } elseif ($origScriptName !== null && basename($origScriptName) === $filename) {
            // 1and1 shared hosting compatibility.
            $baseUrl = $origScriptName;
        } else {
            // Backtrack up the SCRIPT_FILENAME to find the portion
            // matching PHP_SELF.
            $path = $phpSelf ? : '';
            $segments = array_reverse(explode('/', trim($filename, '/')));
            $index = 0;
            $last = count($segments);
            $baseUrl = '';

            do {
                $segment = $segments[$index];
                $baseUrl = '/' . $segment . $baseUrl;
                $index++;
            } while ($last > $index && false !== ($pos = strpos($path, $baseUrl)) && 0 !== $pos);
        }

        // Does the base URL have anything in common with the request URI?
        $requestUri = $this->getRequestUri();

        // Full base URL matches.
        if (0 === strpos($requestUri, $baseUrl)) {
            return $baseUrl;
        }

        // Directory portion of base path matches.
        if (0 === strpos($requestUri, dirname($baseUrl))) {
            $baseUrl = dirname($baseUrl);
            return $baseUrl;
        }

        $truncatedRequestUri = $requestUri;

        if (false !== ($pos = strpos($requestUri, '?'))) {
            $truncatedRequestUri = substr($requestUri, 0, $pos);
        }

        $basename = basename($baseUrl);

        // No match whatsoever
        if (empty($basename) || false === strpos($truncatedRequestUri, $basename)) {
            return '';
        }

        // If using mod_rewrite or ISAPI_Rewrite strip the script filename
        // out of the base path. $pos !== 0 makes sure it is not matching a
        // value from PATH_INFO or QUERY_STRING.
        if (strlen($requestUri) >= strlen($baseUrl)
            && (false !== ($pos = strpos($requestUri, $baseUrl)) && $pos !== 0)
        ) {
            $baseUrl = substr($requestUri, 0, $pos + strlen($baseUrl));
        }

        return $baseUrl;
    }
}
