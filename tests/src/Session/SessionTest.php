<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Session;

use Bluz\Session\Session;
use Bluz\Tests\TestCase;

/**
 * SessionTest
 *
 * @package  Bluz\Tests\Session
 *
 * @author   Anton Shevchuk
 * @created  20.08.2014 13:00
 */
class SessionTest extends TestCase
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * setUp
     */
    public function setUp()
    {
        parent::setUp();

        $this->session = new Session();
        $this->session->setNamespace('testing');
        $this->session->start();
    }

    /**
     * Complex test for setter/getter
     *
     * @covers \Bluz\Session\Session::set()
     * @covers \Bluz\Session\Session::get()
     */
    public function testSetGet()
    {
        $this->assertNull($this->session->get('foo'));

        $this->session->set('foo', 'baz');

        $this->assertEquals('baz', $this->session->get('foo'));
    }

    /**
     * Complex test for __isset
     *
     * @covers \Bluz\Session\Session::contains()
     */
    public function testIsset()
    {
        $this->session->set('moo', 'maz');

        $this->assertTrue($this->session->contains('moo'));
        $this->assertFalse($this->session->contains('boo'));
    }

    /**
     * Complex test for __unset
     *
     * @covers \Bluz\Session\Session::delete()
     */
    public function testUnset()
    {
        $this->session->set('moo', 'maz');
        $this->session->delete('moo');

        $this->assertNull($this->session->get('moo'));
    }
}
