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
     * @return void
     */
    protected function sendHeaders()
    {
        // setup response code
        http_response_code($this->code);

        // send stored headers
        foreach ($this->headers as $key => $value) {
            header($key .': '. $value);
        }

        // TODO: this is application logic
        if (app()->isJson()) {
            // Setup headers
            // HTTP does not define any limit
            // However most web servers do limit size of headers they accept.
            // For example in Apache default limit is 8KB, in IIS it's 16K.
            // Server will return 413 Entity Too Large error if headers size exceeds that limit

            // setup messages
            if (app()->hasMessages()) {
                header('Bluz-Notify: '.json_encode(app()->getMessages()->popAll()));
            }

            // response without content
            if (null === $this->body) {
                return;
            }

            // prepare to JSON output
            $this->body = json_encode($this->body);

            // override response code so javascript can process it
            header('Content-Type: application/json');

            // send content length
            header('Content-Length: '.strlen($this->body));

            if (ob_get_length()) {
                ob_end_clean();
            }
            flush();
        }
    }

    /**
     * Send headers
     *
     * @return void
     */
    protected function sendBody()
    {
        echo $this->body;
    }
}
