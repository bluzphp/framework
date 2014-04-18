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
                echo "\033[41m\033[1;37mError    \033[m\033m: ";
                echo $msg->text . "\n";
            }
            while ($msg = app()->getMessages()->pop(Messages::TYPE_NOTICE)) {
                echo "\033[44m\033[1;37mInfo     \033[m\033m: ";
                echo $msg->text . "\n";
            }
            while ($msg = app()->getMessages()->pop(Messages::TYPE_SUCCESS)) {
                echo "\033[42m\033[1;37mSuccess  \033[m\033m: ";
                echo $msg->text . "\n";
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
            echo "\033[1;33m$key\033[m:\n";
            print_r($value);
            echo "\n";
        }
    }
}
