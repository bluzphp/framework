<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Http;

use Bluz\Http\CacheControl;
use Bluz\Http\Response;
use Bluz\Tests\TestCase;

/**
 * CacheControl test
 *
 * @package  Bluz\Tests\Http
 *
 * @author   Anton Shevchuk
 * @created  06.11.2014 14:37
 */
class CacheControlTest extends TestCase
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * @var CacheControl
     */
    protected $cacheControl;

    /**
     * SetUp
     */
    protected function setUp()
    {
        $this->response =  new Response();
        $this->cacheControl = new CacheControl($this->response);
    }

    /**
     * Test CacheControl as private
     */
    public function testCacheControlPrivate()
    {
        $this->cacheControl->setPrivate();
        $this->cacheControl->setMaxAge(3600);
        $this->assertEquals(3600, $this->cacheControl->getMaxAge());
        $this->assertEquals('max-age=3600, private', $this->response->getHeader('Cache-Control'));
    }

    /**
     * Test CacheControl as public
     */
    public function testCacheControlPublic()
    {
        $this->cacheControl->setSharedMaxAge(3600);
        $this->assertEquals(3600, $this->cacheControl->getMaxAge());
        $this->assertEquals('public, s-maxage=3600', $this->response->getHeader('Cache-Control'));
    }

    /**
     * Test Ttl
     */
    public function testTtl()
    {
        $this->cacheControl->setTtl(3600);
        $this->assertEquals(3600, $this->cacheControl->getTtl());
        $this->assertEquals(3600, $this->cacheControl->getMaxAge());
        $this->assertEquals('public, s-maxage=3600', $this->response->getHeader('Cache-Control'));
    }

    /**
     * Test Client Ttl
     */
    public function testClientTtl()
    {
        $this->cacheControl->setClientTtl(3600);
        $this->assertEquals(3600, $this->cacheControl->getTtl());
        $this->assertEquals(3600, $this->cacheControl->getMaxAge());
        $this->assertEquals('max-age=3600', $this->response->getHeader('Cache-Control'));
    }

    /**
     * Test Age
     */
    public function testAge()
    {
        $this->cacheControl->setAge(3600);
        $this->assertEquals(3600, $this->cacheControl->getAge());
        $this->assertEquals(3600, $this->response->getHeader('Age'));
    }

    /**
     * Test Default Age is zero
     */
    public function testDefaultAge()
    {
        $this->assertEquals(0, $this->cacheControl->getAge());
    }

    /**
     * Test Etag
     */
    public function testEtag()
    {
        $this->cacheControl->setEtag('"unique-content-id"');
        $this->assertEquals('"unique-content-id"', $this->cacheControl->getEtag());
        $this->assertEquals('"unique-content-id"', $this->response->getHeader('ETag'));
    }

    /**
     * Test Expires
     */
    public function testExpiresAsString()
    {
        $this->cacheControl->setExpires('2012-12-12T12:12:12+00:00');
        $this->assertEquals('Wed, 12 Dec 2012 12:12:12 GMT', $this->cacheControl->getExpires());
        $this->assertEquals('Wed, 12 Dec 2012 12:12:12 GMT', $this->response->getHeader('Expires'));
    }

    /**
     * Test Expires
     */
    public function testExpiresAsDate()
    {
        $date = new \DateTime('2012-12-12T12:12:12+00:00');
        $this->cacheControl->setExpires($date);
        $this->assertEquals('Wed, 12 Dec 2012 12:12:12 GMT', $this->cacheControl->getExpires());
        $this->assertEquals('Wed, 12 Dec 2012 12:12:12 GMT', $this->response->getHeader('Expires'));
    }

    /**
     * Test LastModified
     */
    public function testLastModifiedAsString()
    {
        $this->cacheControl->setLastModified('2012-12-12T12:12:12+00:00');
        $this->assertEquals('Wed, 12 Dec 2012 12:12:12 GMT', $this->cacheControl->getLastModified());
        $this->assertEquals('Wed, 12 Dec 2012 12:12:12 GMT', $this->response->getHeader('Last-Modified'));
    }

    /**
     * Test Expires
     */
    public function testLastModifiedAsDate()
    {
        $date = new \DateTime('2012-12-12T12:12:12+00:00');
        $this->cacheControl->setLastModified($date);
        $this->assertEquals('Wed, 12 Dec 2012 12:12:12 GMT', $this->cacheControl->getLastModified());
        $this->assertEquals('Wed, 12 Dec 2012 12:12:12 GMT', $this->response->getHeader('Last-Modified'));
    }

    /**
     * Test Expire
     */
    public function testExpire()
    {
        $this->cacheControl->expire();
        $this->assertEquals(0, $this->cacheControl->getAge());
    }
}
