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
     *
     * @return void
     */
    protected function sendHeaders()
    {
        // output headers
        foreach ($this->getHeaders() as $name => $value) {
            if (!sizeof($value)) {
                continue;
            }
            echo $name .": ". join(',', $value) ."\n";
        }
        if (sizeof($this->headers)) {
            echo "\n";
        }
    }

    /**
     * Send headers
     *
     * @return void
     */
    protected function sendBody()
    {
        $response = $this->body;

        // extract data from view
        if ($response instanceof View) {
            $response = $response->getData();
        }

        // output
        if (is_array($response)) {
            // just print to console
            foreach ($response as $key => $value) {
                echo $key . ": ";
                print_r($value);
                echo "\n";
            }
        } else {
            print_r($response);
        }
    }
}
