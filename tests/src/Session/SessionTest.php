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
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->session = new Session();
        $this->session->setStore('array');
        $this->session->getStore()->start();
    }

    /**
     * Test Destroy session
     * @covers \Bluz\Session\Store\ArrayStore::destroy()
     */
    public function testDestroy()
    {
        $this->session->foo = 'bar';
        $this->session->getStore()->destroy();

        $this->assertNull($this->session->foo);
    }

    /**
     * Complex test for setter/getter
     *
     * @covers \Bluz\Session\Session::__set()
     * @covers \Bluz\Session\Session::__get()
     * @covers \Bluz\Session\Store\ArrayStore::__set()
     * @covers \Bluz\Session\Store\ArrayStore::__get()
     * @return void
     */
    public function testSetGet()
    {
        $this->assertNull($this->session->foo);

        $this->session->foo = 'baz';

        $this->assertEquals('baz', $this->session->foo);
    }

    /**
     * Complex test for __isset
     *
     * @covers \Bluz\Session\Session::__isset()
     * @covers \Bluz\Session\Store\ArrayStore::__isset()
     * @return void
     */
    public function testIsset()
    {
        $this->session->moo = 'maz';

        $this->assertTrue(isset($this->session->moo));
        $this->assertFalse(isset($this->session->boo));
    }

    /**
     * Complex test for __unset
     *
     * @covers \Bluz\Session\Session::__unset()
     * @covers \Bluz\Session\Store\ArrayStore::__unset()
     * @return void
     */
    public function testUnset()
    {
        $this->session->moo = 'maz';
        unset($this->session->moo);

        $this->assertNull($this->session->moo);
    }
}
