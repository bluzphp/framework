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
namespace Bluz\Request;

use Bluz\Common\Options;

/**
 * Request
 *
 * @package  Bluz\Request
 *
 * @author   Anton Shevchuk
 * @created  06.07.11 16:59
 */
class AbstractRequest
{
    use Options;

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
     * Command line request
     */
    const METHOD_CLI = 'CLI';

    /**
     * HTTP Request
     */
    const METHOD_HTTP = 'HTTP';

    /**
     * REQUEST_URI
     * @var string;
     */
    protected $requestUri;

    /**
     * REQUEST_URI minus Base URL
     * @var string;
     */
    protected $cleanUri;

    /**
     * Base URL
     * @var string;
     */
    protected $baseUrl;

    /**
     * Base Path
     * @var string;
     */
    protected $basePath;

    /**
     * HTTP Method or CLI
     * @var string
     */
    protected $method;

    /**
     * Module
     * @var string
     */
    protected $module = 'index';

    /**
     * Controller
     * @var string
     */
    protected $controller = 'index';

    /**
     * Instance parameters
     * @var array
     */
    protected $params = array();

    /**
     * Instance raw parameters
     * @var array
     */
    protected $rawParams = array();

    /**
     * Retrieve the module name
     *
     * @param string $name
     * @return self
     */
    public function setModule($name)
    {
        $this->module = $name;
        return $this;
    }

    /**
     * Retrieve the module name
     *
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Retrieve the controller name
     *
     * @param string $name
     * @return self
     */
    public function setController($name)
    {
        $this->controller = $name;
        return $this;
    }

    /**
     * Retrieve the controller name
     *
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Access values
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return (isset($this->params[$key]) ? $this->params[$key] : null);
    }

    /**
     * Set an action parameter
     *
     * A $value of null will unset the $key if it exists
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function __set($key, $value)
    {
        $key = (string)$key;

        if ((null === $value) && isset($this->params[$key])) {
            unset($this->params[$key]);
        } elseif (null !== $value) {
            $this->params[$key] = $value;
        }
    }

    /**
     * Check to see if a property is set
     *
     * @param string $key
     * @return boolean
     */
    public function __isset($key)
    {
        return isset($this->params[$key]);
    }

    /**
     * Unset custom param
     *
     * @param $key
     */
    public function __unset($key)
    {
        unset($this->params[$key]);
    }

    /**
     * Set an action parameter
     *
     * A $value of null will unset the $key if it exists
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setParam($key, $value)
    {
        $this->__set($key, $value);
    }

    /**
     * Get an action parameter
     *
     * @param string $key
     * @param mixed $default Default value to use if key not found
     * @return mixed
     */
    public function getParam($key, $default = null)
    {
        return $this->__get($key) ? : $default;
    }

    /**
     * Overwrite all parameters
     *
     * @param array $params
     * @return AbstractRequest
     */
    public function setParams(array $params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * Get parameters
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Set raw params, w/out module and controller
     *
     * @param array $params
     * @return array
     */
    public function setRawParams(array $params)
    {
        $this->rawParams = $params;
        return $this;
    }

    /**
     * Get raw params, w/out module and controller
     *
     * @return array
     */
    public function getRawParams()
    {
        return $this->rawParams;
    }


    /**
     * Get all request parameters
     *
     * @return array
     */
    public function getAllParams()
    {
        return $this->params;
    }

    /**
     * Retrieve a member of the $_ENV superglobal
     *
     * If no $key is passed, returns the entire $_ENV array.
     *
     * @param string $key
     * @param mixed $default Default value to use if key not found
     * @return mixed Returns null if key does not exist
     */
    public function getEnv($key = null, $default = null)
    {
        if (null === $key) {
            return $_ENV;
        }

        return (isset($_ENV[$key])) ? $_ENV[$key] : $default;
    }

    /**
     * Return HTTP method or CLI
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * setMethod
     *
     * Overwrite method
     *
     * @param $method
     * @return AbstractRequest
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * check CLI
     *
     * @return boolean
     */
    public function isCli()
    {
        return $this->method == self::METHOD_CLI;
    }

    /**
     * check HTTP
     *
     * @return boolean
     */
    public function isHttp()
    {
        return $this->method == self::METHOD_HTTP;
    }

    /**
     * Set the base URL.
     *
     * @param  string $baseUrl
     * @return self
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = rtrim($baseUrl, '/') . '/';
        return $this;
    }

    /**
     * Set the request URI.
     *
     * @param  string $requestUri
     * @return self
     */
    public function setRequestUri($requestUri)
    {
        $this->requestUri = $requestUri;
        return $this;
    }

    /**
     * Get the request URI.
     *
     * @return string
     */
    public function getRequestUri()
    {
        return $this->requestUri;
    }
}
