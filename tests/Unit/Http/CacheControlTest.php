<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/skeleton
 */

namespace Bluz\Tests\Unit\Http;

use Bluz\Http\CacheControl;
use Bluz\Response\Response;
use Bluz\Tests\Unit\Unit;

/**
 * Crud TableTest
 *
 * @package  Bluz\Tests\Crud
 *
 * @author   Anton Shevchuk
 * @created  22.08.2014 16:13
 */
class CacheControlTest extends Unit
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
     * setUp
     */
    public function setUp(): void
    {
        $this->response = new Response();
        $this->cacheControl = new CacheControl($this->response);
    }

    /**
     * @param string $header
     */
    public function assertHeader($header)
    {
        $cache = $this->response->getHeader('Cache-Control');
        self::assertEquals($header, $cache);
    }

    public function testSetPrivateHeader()
    {
        $this->cacheControl->setPrivate();
        $this->assertHeader('private');
    }

    public function testSetPublicHeader()
    {
        $this->cacheControl->setPublic();
        $this->assertHeader('public');
    }

    public function testSetMaxAge()
    {
        $this->cacheControl->setMaxAge(60);
        $this->assertHeader('max-age=60');
        self::assertEquals('60', $this->cacheControl->getMaxAge());
    }

    public function testGetNullTtl()
    {
        self::assertNull($this->cacheControl->getTtl());
    }

    public function testGetTtl()
    {
        $this->cacheControl->setMaxAge(60);
        self::assertEquals(60, $this->cacheControl->getTtl());
    }

    public function testSetTtl()
    {
        $this->cacheControl->setTtl(60);
        $this->assertHeader('public, s-maxage=60');
    }

    public function testSetClientTtl()
    {
        $this->cacheControl->setClientTtl(60);
        $this->assertHeader('max-age=60');
    }

    public function testSetEtag()
    {
        $this->cacheControl->setEtag('some-etag');
        self::assertEquals(
            $this->cacheControl->getEtag(),
            $this->response->getHeader('ETag')
        );
    }

    public function testSetSharedMaxAge()
    {
        $this->cacheControl->setSharedMaxAge(60);
        $this->assertHeader('public, s-maxage=60');
        self::assertEquals('60', $this->cacheControl->getMaxAge());
    }

    public function testSetExpires()
    {
        $this->cacheControl->setExpires('2001-01-01 01:01:01');
        self::assertEquals(
            $this->cacheControl->getExpires(),
            $this->response->getHeader('Expires')
        );
        self::assertLessThan(-500000000, $this->cacheControl->getMaxAge());
    }

    public function testSetLastModified()
    {
        $this->cacheControl->setLastModified('2001-01-01 01:01:01');
        self::assertEquals(
            $this->cacheControl->getLastModified(),
            $this->response->getHeader('Last-Modified')
        );
        self::assertLessThan(-500000000, $this->cacheControl->getMaxAge());
    }
}
