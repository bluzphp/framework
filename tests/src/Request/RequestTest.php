<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Request;

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
        $this->assertTrue($this->getApp()->getRequest()->isHttp());
        $this->assertFalse($this->getApp()->getRequest()->isCli());
    }

    /**
     * Test of params
     */
    public function testParamManipulation()
    {
        $this->getApp()->getRequest()->setParam('foo', 'bar');
        $this->getApp()->getRequest()->setParam('baz', 'qux');

        $this->assertEquals('bar', $this->getApp()->getRequest()->getParam('foo'));
        $this->assertEquals('qux', $this->getApp()->getRequest()->getParam('baz'));
        $this->assertEquals('moo', $this->getApp()->getRequest()->getParam('qux', 'moo'));

        $this->assertEqualsArray(['foo' => 'bar', 'baz' => 'qux'], $this->getApp()->getRequest()->getParams());
        $this->assertEqualsArray(['foo' => 'bar', 'baz' => 'qux'], $this->getApp()->getRequest()->getAllParams());
    }
}
