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
namespace Bluz\Response;

use Bluz\Common\Options;

/**
 * AbstractResponse
 *
 * @package  Bluz\Response
 *
 * @author   Anton Shevchuk
 * @created  18.02.14 11:11
 */
abstract class AbstractResponse
{
    use Options;

    /**
     * Response code equal to HTTP status codes
     * @var int
     */
    protected $code = 200;

    /**
     * Stack of headers
     * @var array
     */
    protected $headers = array();

    /**
     * Result can be View|object|function
     * @var mixed
     */
    protected $body;

    /**
     * Catched exception
     * @var \Exception
     */
    protected $exception;

    /**
     * Send messages to client
     * @return mixed
     */
    abstract protected function sendHeaders();

    /**
     * Send messages to client
     * @return mixed
     */
    abstract protected function sendBody();

    /**
     * Send data to client (console or browser)
     *
     * @access  public
     */
    public function send()
    {
        $this->sendHeaders();
        $this->sendBody();
    }

    /**
     * setCode
     *
     * @param integer $code
     * @return AbstractResponse
     */
    public function setCode($code)
    {
        $this->code = (int) $code;
        return $this;
    }

    /**
     * getCode
     *
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * setup headers
     *
     * @param array $headers
     * @return AbstractResponse
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * get headers
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * clear all headers
     *
     * @return AbstractResponse
     */
    public function clearHeaders()
    {
        $this->headers = array();
        return $this;
    }

    /**
     * add/set header
     *
     * @param string $key header name
     * @param string $value header value
     * @return AbstractResponse
     */
    public function setHeader($key, $value)
    {
        $this->headers[$key] = $value;
        return $this;
    }

    /**
     * get header
     *
     * @param string $key header name
     * @return string
     */
    public function getHeader($key)
    {
        if (isset($this->headers[$key])) {
            return $this->headers[$key];
        } else {
            return false;
        }
    }

    /**
     * setBody
     *
     * @param mixed $body
     * @return AbstractResponse
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * getBody
     *
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * clearBody
     *
     * @return AbstractResponse
     */
    public function clearBody()
    {
        $this->body = null;
        return $this;
    }

    /**
     * setException
     *
     * @param \Exception $exception
     * @return AbstractResponse
     */
    public function setException($exception)
    {
        $this->clearHeaders();
        $this->clearBody();
        $this->exception = $exception;
        return $this;
    }

    /**
     * getException
     *
     * @return \Exception|null
     */
    public function getException()
    {
        return $this->exception;
    }
}
