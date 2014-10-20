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

use Bluz\Response\AbstractResponse;

/**
 * Response
 *
 * @category Http
 * @package  Bluz\Response
 *
 * @author   Anton Shevchuk
 * @created  18.02.14 11:57
 */
class Response extends AbstractResponse
{
    /**
     * Send headers
     *
     * HTTP does not define any limit
     * However most web servers do limit size of headers they accept.
     * For example in Apache default limit is 8KB, in IIS it's 16K.
     * Server will return 413 Entity Too Large error if headers size exceeds that limit
     *
     * @return void
     */
    protected function sendHeaders()
    {
        // setup response code
        http_response_code($this->code);

        // send stored headers
        foreach ($this->headers as $key => $value) {
            header($key .': '. join(', ', $value));
        }
    }

    /**
     * Send body
     * @return void
     */
    protected function sendBody()
    {
        echo $this->body;
    }
}
