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

use Bluz\View\View;

/**
 * Json
 *
 * @package  Bluz\Response\Presentation
 *
 * @author   Anton Shevchuk
 * @created  10.11.2014 18:03
 */
class Cli extends AbstractPresentation
{
    /**
     * Response to CLI
     */
    public function process()
    {
        // prepare body
        if ($body = $this->response->getBody()) {
            // extract data from view
            if ($body instanceof View) {
                $body = $body->toArray();
            }

            // output
            if (is_array($body)) {
                // just print to console as key-value pair
                $output = array();

                array_walk_recursive($body, function ($value, $key) use (&$output) {
                    $output[] = $key .': '. $value;
                });

                $body = join("\n", $output);
            }
            $this->response->setBody($body . "\n");
        }
    }
}
