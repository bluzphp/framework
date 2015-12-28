<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Http;

use Bluz\Http\Response;
use Bluz\Tests\TestCase;

/**
 * ResponseTest
 *
 * @package  Bluz\Tests\Http
 *
 * @author   Anton Shevchuk
 * @created  20.08.2014 10:47
 */
class ResponseTest extends TestCase
{
    /**
     * Test Response Body
     */
    public function testResponseBody()
    {
        $this->expectOutputString('foo');

        $response = new Response();
        $response->setBody('foo');

        $this->assertEquals('foo', $response->getBody());

        $response->send();
    }
}
