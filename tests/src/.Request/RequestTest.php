<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Request;

use Bluz\Proxy\Request;
use Bluz\Tests\TestCase;

/**
 * @package  Bluz\Tests\Http
 *
 * @author   Anton Shevchuk
 * @created  21.08.2014 10:08
 */
class RequestTest extends TestCase
{
    /**
     * Test `Is` Methods
     */
    public function testIsMethods()
    {
        $this->assertTrue(Request::isHttp());
        $this->assertFalse(Request::isCli());
    }

    /**
     * Test of params
     */
    public function testParamManipulation()
    {
        Request::setParam('foo', 'bar');
        Request::setParam('baz', 'qux');

        $this->assertEquals('bar', Request::getParam('foo'));
        $this->assertEquals('qux', Request::getParam('baz'));
        $this->assertEquals('moo', Request::getParam('qux', 'moo'));

        $this->assertEqualsArray(['foo' => 'bar', 'baz' => 'qux'], Request::getParams());
        $this->assertEqualsArray(['foo' => 'bar', 'baz' => 'qux'], Request::getAllParams());
    }
}
