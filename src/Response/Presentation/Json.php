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
namespace Bluz\Response\Presentation;

use Bluz\Proxy\Messages;

/**
 * Json
 *
 * @package  Bluz\Response\Presentation
 *
 * @author   Anton Shevchuk
 * @created  10.11.2014 18:03
 */
class Json extends AbstractPresentation
{
    /**
     * Response as Json
     */
    public function process()
    {
        // override response code so javascript can process it
        $this->response->setHeader('Content-Type', 'application/json');

        // setup messages
        if (Messages::count()) {
            $this->response->setHeader('Bluz-Notify', json_encode(Messages::popAll()));
        }

        // prepare body
        if ($body = $this->response->getBody()) {
            // convert to JSON
            $body = json_encode($body);

            // setup content length
            $this->response->setHeader('Content-Length', strlen($body));

            // prepare to JSON output
            $this->response->setBody($body);
        }
    }
}
