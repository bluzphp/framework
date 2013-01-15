<?php
/**
 * Copyright (c) 2012 by Bluz PHP Team
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * @namespace
 */
namespace Bluz\Request;

/**
 * Request
 *
 * @category Bluz
 * @package  Request
 *
 * @author   Anton Shevchuk
 * @created  06.07.11 16:59
 */
class AbstractRequest
{
    use \Bluz\Package;

    /**
     * @const string SCHEME constant names
     */
    const SCHEME_HTTP  = 'http';
    const SCHEME_HTTPS = 'https';

    /**
     * @const string METHOD constant names
     */
    const METHOD_OPTIONS = 'OPTIONS';
    const METHOD_GET     = 'GET';
    const METHOD_HEAD    = 'HEAD';
    const METHOD_POST    = 'POST';
    const METHOD_PUT     = 'PUT';
    const METHOD_DELETE  = 'DELETE';
    const METHOD_TRACE   = 'TRACE';
    const METHOD_CONNECT = 'CONNECT';

    /**
     * Command line interface only
     */
    const METHOD_CLI = 'CLI';

    /**
     * REQUEST_URI
     *
     * @var string;
     */
    protected $requestUri;

    /**
     * REQUEST_URI minus Base URL
     *
     * @var string;
     */
    protected $cleanUri;

    /**
     * Base URL
     *
     * @var string;
     */
    protected $baseUrl;

    /**
     * Base Path
     *
     * @var string;
     */
    protected $basePath;

    /**
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
        $key = (string) $key;

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
     * @deprecated
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
        return $this->__get($key)?:$default;
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
     * getMethod
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
     * Is this a POST method request?
     *
     * @return bool
     */
    public function isPost()
    {
        return ($this->getMethod() === self::METHOD_POST);
    }

    /**
     * Is the request a Javascript XMLHttpRequest?
     *
     * Should work with Prototype/Script.aculo.us, possibly others.
     *
     * @return boolean
     */
    public function isXmlHttpRequest()
    {
        return ($this->getHeader('X_REQUESTED_WITH') == 'XMLHttpRequest');
    }

    /**
     * Is this a Flash request?
     *
     * @return boolean
     */
    public function isFlashRequest()
    {
        $header = strtolower($this->getHeader('USER_AGENT'));
        return (strstr($header, ' flash')) ? true : false;
    }

    /**
     * Return the value of the given HTTP header. Pass the header name as the
     * plain, HTTP-specified header name. Ex.: Ask for 'Accept' to get the
     * Accept header, 'Accept-Encoding' to get the Accept-Encoding header.
     *
     * @param string $header HTTP header name
     * @return string|boolean HTTP header value, or false if not found
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
}
