<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Response;

use Bluz\Http\Response;
use Bluz\Tests\TestCase;

/**
 * @package  Bluz\Tests\Http
 *
 * @author   Anton Shevchuk
 * @created  22.08.2014 11:18
 */
class ResponseTest extends TestCase
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * SetUp
     */
    public function setUp()
    {
        $this->response = new Response();
    }

    /**
     * @covers \Bluz\Response\AbstractResponse::setHeader
     * @covers \Bluz\Response\AbstractResponse::getHeader
     * @covers \Bluz\Response\AbstractResponse::hasHeader
     */
    public function testSetGetHasHeader()
    {
        $this->response->setHeader('foo', 'bar');

        $this->assertTrue($this->response->hasHeader('foo'));
        $this->assertEquals('bar', $this->response->getHeader('foo'));
    }

    /**
     * @covers \Bluz\Response\AbstractResponse::addHeader
     * @covers \Bluz\Response\AbstractResponse::getHeader
     * @covers \Bluz\Response\AbstractResponse::getHeaderAsArray
     */
    public function testAddHeader()
    {
        $this->response->setHeader('foo', 'bar');
        $this->response->addHeader('foo', 'baz');

        $this->assertTrue($this->response->hasHeader('foo'));
        $this->assertEquals('bar, baz', $this->response->getHeader('foo'));
        $this->assertEqualsArray(['bar', 'baz'], $this->response->getHeaderAsArray('foo'));
    }

    /**
     * @covers \Bluz\Response\AbstractResponse::setHeaders
     * @covers \Bluz\Response\AbstractResponse::addHeaders
     * @covers \Bluz\Response\AbstractResponse::getHeaders
     */
    public function testAddHeaders()
    {
        $this->response->setHeaders(['foo' => ['bar']]);
        $this->response->addHeaders(['foo' => ['baz'], 'baz' => ['qux']]);

        $this->assertEquals(2, sizeof($this->response->getHeaders()));
        $this->assertArrayHasKeyAndSize($this->response->getHeaders(), 'foo', 2);
    }
}
