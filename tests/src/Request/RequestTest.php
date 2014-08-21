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
     * Reset Application
     */
    public static function tearDownAfterClass()
    {
        self::resetApp();
    }

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
        $this->getApp()->getRequest()->foo = 'bar';
        $this->getApp()->getRequest()->setParam('baz', 'qux');

        $this->assertTrue(isset($this->getApp()->getRequest()->foo));
        $this->assertEquals('bar', $this->getApp()->getRequest()->foo);
        $this->assertEquals('bar', $this->getApp()->getRequest()->getParam('foo'));

        $this->assertTrue(isset($this->getApp()->getRequest()->baz));
        $this->assertEquals('qux', $this->getApp()->getRequest()->baz);
        $this->assertEquals('qux', $this->getApp()->getRequest()->getParam('baz'));

        $this->assertEqualsArray(['foo' => 'bar', 'baz' => 'qux'], $this->getApp()->getRequest()->getParams());
        $this->assertEqualsArray(['foo' => 'bar', 'baz' => 'qux'], $this->getApp()->getRequest()->getAllParams());
    }

    /**
     * Test unset of params
     */
    public function testParamUnset()
    {
        $this->getApp()->getRequest()->foo = 'bar';
        $this->getApp()->getRequest()->baz = 'qux';

        unset($this->getApp()->getRequest()->foo);
        $this->getApp()->getRequest()->setParam('baz', null);

        $this->assertFalse(isset($this->getApp()->getRequest()->foo));
        $this->assertFalse(isset($this->getApp()->getRequest()->baz));
    }
}
