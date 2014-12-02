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
     * Test initial values of Response properties
     */
    public function testGetters()
    {
        $this->assertNotEmpty($this->response->getProtocolVersion());
        $this->assertNull($this->response->getReasonPhrase());
        $this->assertNull($this->response->getException());
    }

    /**
     * Test initial values of Response properties
     */
    public function testSetGetReasonPhrase()
    {
        $this->response->setReasonPhrase('OK');
        $this->assertEquals('OK', $this->response->getReasonPhrase());
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
        $this->assertEmpty($this->response->getHeader('baz'));
    }

    /**
     * @covers \Bluz\Response\AbstractResponse::addHeader
     * @covers \Bluz\Response\AbstractResponse::getHeader
     * @covers \Bluz\Response\AbstractResponse::getHeaderAsArray
     */
    public function testAddHeader()
    {
        $this->response->addHeader('foo', 'bar');
        $this->response->addHeader('foo', 'baz');

        $this->assertTrue($this->response->hasHeader('foo'));
        $this->assertEquals('bar, baz', $this->response->getHeader('foo'));
        $this->assertEqualsArray(['bar', 'baz'], $this->response->getHeaderAsArray('foo'));
        $this->assertEqualsArray([], $this->response->getHeaderAsArray('baz'));
    }

    /**
     * @covers \Bluz\Response\AbstractResponse::removeHeader
     */
    public function testRemoveHeader()
    {
        $this->response->addHeader('foo', 'bar');
        $this->response->removeHeader('foo');

        $this->assertFalse($this->response->hasHeader('foo'));
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

    /**
     * @covers \Bluz\Response\AbstractResponse::removeHeaders
     */
    public function testRemoveHeaders()
    {
        $this->response->addHeader('foo', 'bar');
        $this->response->addHeader('baz', 'qux');
        $this->response->removeHeaders();

        $this->assertFalse($this->response->hasHeader('foo'));
        $this->assertFalse($this->response->hasHeader('baz'));
    }

    /**
     * @covers \Bluz\Response\AbstractResponse::setCookie
     * @covers \Bluz\Response\AbstractResponse::getCookie
     */
    public function testSetGetCookies()
    {
        $this->response->setCookie('foo', 'bar');
        $this->assertEqualsArray(['foo', 'bar', 0, '/', null, false, true], $this->response->getCookie('foo'));
    }

    /**
     * Set cookie expire time as DateTime object
     */
    public function testSetCookiesWithDatetime()
    {
        $dateTime = new \DateTime('now');
        $this->response->setCookie('foo', 'bar', $dateTime);
        $this->assertEqualsArray(
            ['foo', 'bar', $dateTime->format('U'), '/', null, false, true],
            $this->response->getCookie('foo')
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetCookieWithWrongCookieNameThrowException()
    {
        $this->response->setCookie('foo=', 'bar');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetCookieWithEmptyCookieNameThrowException()
    {
        $this->response->setCookie('', 'bar');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetCookieWithWrongDateNameThrowException()
    {
        $this->response->setCookie('foo', 'bar', 'the day before sunday');
    }
}
