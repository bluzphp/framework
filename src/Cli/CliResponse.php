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

use InvalidArgumentException;
use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;

/**
 * CLI response.
 *
 * Allows creating a response by passing data to the constructor; by default,
 * serializes the data to JSON, sets a status code of 200 and sets the
 * Content-Type header to application/json.
 */
class CliResponse extends Response
{
    /**
     * Create a console response with the given data.
     *
     * @param mixed $data Data to convert to string
     * @param int $status Integer status code for the response; 200 by default.
     * @throws InvalidArgumentException if unable to encode the $data to JSON.
     */
    public function __construct($data, $status = 200)
    {
        $body = new Stream('php://temp', 'wb+');
        $body->write($this->encode($data));
        parent::__construct($body, $status);
    }

    /**
     * Encode the provided data to JSON.
     *
     * @param mixed $data
     * @return string
     * @throws InvalidArgumentException if unable to encode the $data to JSON.
     */
    private function encode($data)
    {
        if (is_resource($data)) {
            throw new InvalidArgumentException('Cannot encode resources');
        }

        // just print to console as key-value pair
        $output = array();
        array_walk_recursive($data, function ($value, $key) use (&$output) {
            $output[] = $key .': '. $value;
        });

        return join("\n", $output) . "\n";
    }
}
