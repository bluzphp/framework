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
            while ($msg = app()->getMessages()->pop(Messages::TYPE_ERROR)) {
                echo Colorize::text("Error   ", "white", "red", true) . ": ". $msg->text . "\n";
            }
            while ($msg = app()->getMessages()->pop(Messages::TYPE_NOTICE)) {
                echo Colorize::text("Info    ", "white", "blue", true) . ": ". $msg->text . "\n";
            }
            while ($msg = app()->getMessages()->pop(Messages::TYPE_SUCCESS)) {
                echo Colorize::text("Success ", "white", "green", true) . ": ". $msg->text . "\n";
            }
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

        // just print to console
        foreach ($response as $key => $value) {
            echo Colorize::text($key, "yellow", null, true) . ": ";
            print_r($value);
            echo "\n";
        }
    }
}
