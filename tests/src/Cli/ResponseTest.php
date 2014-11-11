<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Cli;

use Bluz\Cli\Response;
use Bluz\Tests\TestCase;
use Bluz\View\View;

/**
 * ResponseTest
 *
 * @package  Bluz\Tests\Cli
 *
 * @author   Anton Shevchuk
 * @created  22.08.2014 10:00
 */
class ResponseTest extends TestCase
{
    /**
     * Test Response Body
     */
    public function testSendBody()
    {
        $this->expectOutputString("foo\n");

        $response = new Response();
        $response->setBody('foo');

        $this->assertEquals('foo', $response->getBody());

        $response->send();
    }

    /**
     * Test Response Body
     */
    public function testSendBodyWithView()
    {
        $this->expectOutputString("foo: bar\n");

        $view = new View();
        $view->foo = 'bar';

        $response = new Response();
        $response->setBody($view);
        $response->send();
    }
}
