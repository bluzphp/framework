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
namespace Bluz\Cli;

use Bluz\Response\AbstractResponse;
use Bluz\View\View;

/**
 * Response
 *
 * @package  Bluz\Cli
 *
 * @author   Anton Shevchuk
 * @created  18.02.14 11:57
 */
class Response extends AbstractResponse
{
    /**
     * Send headers
     * @return void
     */
    protected function sendHeaders()
    {
        // no output headers
    }

    /**
     * Send headers
     * @return void
     */
    protected function sendBody()
    {
        $response = $this->body;

        // extract data from view
        if ($response instanceof View) {
            $response = $response->toArray();
        }

        // output
        if (is_array($response)) {
            // just print to console
            foreach ($response as $key => $value) {
                echo "$key: $value\n";
            }
        } else {
            echo $response;
        }
    }
}
