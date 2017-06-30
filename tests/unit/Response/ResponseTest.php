<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */

namespace Bluz\Tests\Response;

use Bluz\Response\Response;
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
        self::assertNotEmpty($this->response->getProtocolVersion());
        self::assertNull($this->response->getReasonPhrase());
    }

    /**
     * Test initial values of Response properties
     */
    public function testSetGetReasonPhrase()
    {
        $this->response->setReasonPhrase('OK');
        self::assertEquals('OK', $this->response->getReasonPhrase());
    }

    /**
     * @covers \Bluz\Response\Response::setHeader
     * @covers \Bluz\Response\Response::getHeader
     * @covers \Bluz\Response\Response::hasHeader
     */
    public function testSetGetHasHeader()
    {
        $this->response->setHeader('foo', 'bar');

        self::assertTrue($this->response->hasHeader('foo'));
        self::assertEquals('bar', $this->response->getHeader('foo'));
        self::assertEmpty($this->response->getHeader('baz'));
    }

    /**
     * @covers \Bluz\Response\Response::addHeader
     * @covers \Bluz\Response\Response::getHeader
     * @covers \Bluz\Response\Response::getHeaderAsArray
     */
    public function testAddHeader()
    {
        $this->response->addHeader('foo', 'bar');
        $this->response->addHeader('foo', 'baz');

        self::assertTrue($this->response->hasHeader('foo'));
        self::assertEquals('bar, baz', $this->response->getHeader('foo'));
        self::assertEqualsArray(['bar', 'baz'], $this->response->getHeaderAsArray('foo'));
        self::assertEqualsArray([], $this->response->getHeaderAsArray('baz'));
    }

    /**
     * @covers \Bluz\Response\Response::removeHeader
     */
    public function testRemoveHeader()
    {
        $this->response->addHeader('foo', 'bar');
        $this->response->removeHeader('foo');

        self::assertFalse($this->response->hasHeader('foo'));
    }

    /**
     * @covers \Bluz\Response\Response::setHeaders
     * @covers \Bluz\Response\Response::addHeaders
     * @covers \Bluz\Response\Response::getHeaders
     */
    public function testAddHeaders()
    {
        $this->response->setHeaders(['foo' => ['bar']]);
        $this->response->addHeaders(['foo' => ['baz'], 'baz' => ['qux']]);

        self::assertCount(2, $this->response->getHeaders());
        self::assertArrayHasKeyAndSize($this->response->getHeaders(), 'foo', 2);
    }

    /**
     * @covers \Bluz\Response\Response::removeHeaders
     */
    public function testRemoveHeaders()
    {
        $this->response->addHeader('foo', 'bar');
        $this->response->addHeader('baz', 'qux');
        $this->response->removeHeaders();

        self::assertFalse($this->response->hasHeader('foo'));
        self::assertFalse($this->response->hasHeader('baz'));
    }

    /**
     * @covers \Bluz\Response\Response::setCookie
     * @covers \Bluz\Response\Response::getCookie
     */
    public function testSetGetCookies()
    {
        $this->response->setCookie('foo', 'bar');
        self::assertEqualsArray(['foo', 'bar', 0, '/', null, false, false], $this->response->getCookie('foo'));
    }

    /**
     * Set cookie expire time as DateTime object
     */
    public function testSetCookiesWithDatetime()
    {
        $dateTime = new \DateTime('now');
        $this->response->setCookie('foo', 'bar', $dateTime);
        self::assertEqualsArray(
            ['foo', 'bar', $dateTime->format('U'), '/', null, false, false],
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
