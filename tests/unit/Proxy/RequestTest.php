<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/skeleton
 */

namespace Bluz\Tests\Proxy;

use Bluz\Http\RequestMethod;
use Bluz\Proxy\Request;
use Bluz\Tests\FrameworkTestCase;

/**
 * RequestTest
 *
 * @package  Bluz\Tests\Proxy
 * @author   Anton Shevchuk
 */
class RequestTest extends FrameworkTestCase
{
    /**
     * Test $_ENV variables
     */
    public function testEnvParams()
    {
        $_ENV['foo'] = 'bar';

        self::setRequestParams('/');

        self::assertEquals('bar', Request::getEnv('foo'));
    }

    /**
     * Test $_GET variables
     */
    public function testQueryParams()
    {
        self::setRequestParams('/', ['foo' => 'bar']);

        self::assertEquals('bar', Request::getQuery('foo'));
        self::assertEquals('bar', Request::getParam('foo'));
    }

    /**
     * Test $_POST variables
     */
    public function testParsedBodyParams()
    {
        self::setRequestParams('/', [], ['foo' => 'bar'], RequestMethod::POST);

        self::assertTrue(Request::isPost());
        self::assertEquals('bar', Request::getPost('foo'));
        self::assertEquals('bar', Request::getParam('foo'));
    }

    /**
     * Test $_COOKIE variables
     */
    public function testCookieParams()
    {
        self::setRequestParams('/', [], [], RequestMethod::GET, [], ['foo' => 'bar']);

        self::assertTrue(Request::isGet());
        self::assertEquals('bar', Request::getCookie('foo'));
    }

    /**
     * Test merge of params
     */
    public function testGetParams()
    {
        self::setRequestParams('/', ['foo' => 'bar'], ['foo' => 'baz', 'baz' => 'qux']);

        self::assertEqualsArray(['foo' => 'bar', 'baz' => 'qux'], Request::getParams());
    }

    /**
     * Test priority of params
     */
    public function testGetParamPriority()
    {
        self::setRequestParams('/', ['foo' => 'bar'], ['foo' => 'baz']);

        self::assertEquals('bar', Request::getQuery('foo'));
        self::assertEquals('baz', Request::getPost('foo'));
        self::assertEquals('bar', Request::getParam('foo'));
    }

    /**
     * Test default value of params
     */
    public function testGetParamDefaultValue()
    {
        self::setRequestParams('/');

        self::assertEquals('bar', Request::getParam('foo', 'bar'));
    }

    public function testGetClienIp()
    {
        self::setRequestParams('/');

        self::assertNull(Request::getClientIp());
        self::assertNull(Request::getClientIp(true));
    }

    public function testIsCli()
    {
        self::setRequestParams('/');

        self::assertTrue(Request::isCli());
    }

    public function testIsGet()
    {
        self::setRequestParams('/', [], [], RequestMethod::GET);

        self::assertTrue(Request::isGet());
    }

    public function testIsPost()
    {
        self::setRequestParams('/', [], [], RequestMethod::POST);

        self::assertTrue(Request::isPost());
    }

    public function testIsPut()
    {
        self::setRequestParams('/', [], [], RequestMethod::PUT);

        self::assertTrue(Request::isPut());
    }

    public function testIsDelete()
    {
        self::setRequestParams('/', [], [], RequestMethod::DELETE);

        self::assertTrue(Request::isDelete());
    }
}
