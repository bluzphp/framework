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

use Bluz\Messages\Messages;
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
        // output messages if exists
        if (app()->hasMessages()) {
            $messages = app()->getMessages()->popAll();
            foreach ($messages as $type => $stack) {
                if (!sizeof($stack)) {
                    continue;
                }
                echo "\n";
                switch ($type) {
                    case Messages::TYPE_ERROR:
                        echo Colorize::text("Errors  ", "white", "red", true);
                        break;
                    case Messages::TYPE_NOTICE:
                        echo Colorize::text("Info    ", "white", "blue", true);
                        break;
                    case Messages::TYPE_SUCCESS:
                        echo Colorize::text("Success ", "white", "green", true);
                        break;
                }
                echo ":\n\t";
                echo join("\n\t", $stack);
            }
            echo "\n\n";
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
                echo Colorize::text($key, "yellow", null, true) . ": ";
                print_r($value);
                echo "\n";
            }
        } else {
            echo Colorize::text("Response", "yellow", null, true) . ": ";
            print_r($response);
            echo "\n";
        }
    }
}
