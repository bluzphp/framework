<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/skeleton
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Proxy;

use Bluz\Proxy\Request;
use Bluz\Tests\TestCase;

/**
 * RequestTest
 *
 * @package  Bluz\Tests\Proxy
 * @author   Anton Shevchuk
 */
class RequestTest extends TestCase
{
    /**
     * Test $_GET variables
     */
    public function testQueryParams()
    {
        $this->setRequestParams('/', ['foo' => 'bar']);

        self::assertEquals('bar', Request::getQuery('foo'));
        self::assertEquals('bar', Request::getParam('foo'));
    }

    /**
     * Test $_POST variables
     */
    public function testParsedBodyParams()
    {
        $this->setRequestParams('/', [], ['foo' => 'bar'], Request::METHOD_POST);

        self::assertEquals('bar', Request::getPost('foo'));
        self::assertEquals('bar', Request::getParam('foo'));
    }

    /**
     * Test $_COOKIE variables
     */
    public function testCookieParams()
    {
        $this->setRequestParams('/', [], [], Request::METHOD_GET, [], ['foo' => 'bar']);

        self::assertEquals('bar', Request::getCookie('foo'));
        self::assertEquals('bar', Request::getParam('foo'));
    }

    /**
     * Test merge of params
     */
    public function testGetParams()
    {
        $this->setRequestParams('/', ['foo' => 'bar'], ['foo' => 'baz', 'baz' => 'qux']);

        self::assertEqualsArray(['foo' => 'bar', 'baz' => 'qux'], Request::getParams());
    }

    /**
     * Test priority of params
     */
    public function testGetParamPriority()
    {
        $this->setRequestParams('/', ['foo' => 'bar'], ['foo' => 'baz']);

        self::assertEquals('bar', Request::getQuery('foo'));
        self::assertEquals('baz', Request::getPost('foo'));
        self::assertEquals('bar', Request::getParam('foo'));
    }

    /**
     * Test default value of params
     */
    public function testGetParamDefaultValue()
    {
        $this->setRequestParams('/');

        self::assertEquals('bar', Request::getParam('foo', 'bar'));
    }
}
