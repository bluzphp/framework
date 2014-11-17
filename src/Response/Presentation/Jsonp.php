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

use Bluz\Proxy\Request;

/**
 * JSONP presentation
 *
 * @package  Bluz\Response\Presentation
 *
 * @author   Anton Shevchuk
 * @created  17.11.2014 13:47
 */
class Jsonp extends AbstractPresentation
{
    /**
     * Response as JSONP
     */
    public function process()
    {
        // override response code so javascript can process it
        $this->response->setHeader('Content-Type', 'application/javascript');

        // prepare body
        if ($body = $this->response->getBody()) {
            // convert to JSON
            $body = json_encode($body);

            // try to guess callback function name
            //  - check `callback` param
            //  - check `jsonp` param
            //  - use `callback` as default callback name
            $callback = Request::getParam('jsonp', Request::getParam('callback', 'callback'));
            $body = $callback .'('. $body .')';

            // setup content length
            $this->response->setHeader('Content-Length', strlen($body));

            // prepare to JSON output
            $this->response->setBody($body);
        }
    }
}
