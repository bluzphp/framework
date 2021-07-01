<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Session;

use Bluz\Session\Session;
use Bluz\Tests\FrameworkTestCase;

/**
 * SessionTest
 *
 * @package  Bluz\Tests\Session
 *
 * @author   Anton Shevchuk
 * @created  20.08.2014 13:00
 */
class SessionTest extends FrameworkTestCase
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
        self::assertNull($this->session->get('foo'));

        $this->session->set('foo', 'baz');

        self::assertEquals('baz', $this->session->get('foo'));
    }

    /**
     * Complex test for __isset
     *
     * @covers \Bluz\Session\Session::contains()
     */
    public function testIsset()
    {
        $this->session->set('moo', 'maz');

        self::assertTrue($this->session->contains('moo'));
        self::assertFalse($this->session->contains('boo'));
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

        self::assertNull($this->session->get('moo'));
    }
}
