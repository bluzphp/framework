<?php
/**
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
 * @category Bluz
 * @package  Response
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

    protected $headers = array();
    protected $body;

    /**
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
        $this->exception = $exception;
        return $this;
    }
}
 