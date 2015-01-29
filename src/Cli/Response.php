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
use Bluz\Response\Presentation\AbstractPresentation;

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
     * @var string|AbstractPresentation Support JSON, XML, CLI
     */
    protected $presentation = 'cli';

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
        // Body can be Closure
        $content = $this->body;
        if ($content instanceof \Closure) {
            $content();
        } else {
            echo $content;
        }
    }
}
