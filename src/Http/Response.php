<?php
/**
 * @namespace
 */
namespace Bluz\Http;

use Bluz\Response\AbstractResponse;
use Bluz\Application\Exception\RedirectException;
use Bluz\Application\Exception\ReloadException;

/**
 * Response
 *
 * @category Http
 * @package  Response
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

        /**
         * - Why you don't use "X-" prefix?
         * - Because it deprecated
         * @link http://tools.ietf.org/html/rfc6648
         */
        if ($this->exception && $this->exception instanceof RedirectException) {
            if (app()->getRequest()->isXmlHttpRequest()) {
                header('Bluz-Redirect: ' . $this->exception->getMessage(), true, 204);
            } else {
                header('Location: ' . $this->exception->getMessage(), true, $this->exception->getCode());
            }
            return;
        } elseif ($this->exception && $this->exception instanceof ReloadException) {
            if (app()->getRequest()->isXmlHttpRequest()) {
                header('Bluz-Reload: true', true, 204);
            } else {
                header('Refresh: 15; url=' . app()->getRequest()->getRequestUri());
            }
            return;
        }

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
            if (false === $this->body) {
                return;
            }

            // prepare to JSON output
            $this->body = json_encode($this->body);

            // override response code so javascript can process it
            header('Content-Type: application/json');

            // send content length
            header('Content-Length: '.strlen($this->body));

            ob_clean();
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
        if (app()->hasLayout()) {
            app()->getLayout()->setContent($this->body);
            echo app()->getLayout();
        } else {
            /**
             * Can be Closure or any object with magic method '__invoke'
             */
            if (is_callable($this->body)) {
                $this->body = call_user_func($this->body);
            }
            echo $this->body;
        }
    }
}
