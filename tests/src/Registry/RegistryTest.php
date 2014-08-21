<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Registry;

use Bluz;
use Bluz\Registry\Registry;
use Bluz\Tests\TestCase;

/**
 * RegistryTest
 *
 * @package  Bluz\Tests
 *
 * @author   Anton Shevchuk
 * @created  14.05.2014 11:09
 */
class RegistryTest extends TestCase
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * setUp
     */
    public function setUp()
    {
        parent::setUp();

        $this->registry = new Registry();
    }

    /**
     * setDataTest
     *
     * @covers \Bluz\Registry\Registry::setData()
     */
    public function testSetData()
    {
        $data = ['foo' => 'bar'];

        $this->registry->setData($data);

        $this->assertEquals('bar', $this->registry->__get('foo'));
    }

    /**
     * Complex test for setter/getter
     *
     * @covers \Bluz\Registry\Registry::__set()
     * @covers \Bluz\Registry\Registry::__get()
     */
    public function testSetGet()
    {
        $this->assertNull($this->registry->foo);

        $this->registry->foo = 'baz';

        $this->assertEquals('baz', $this->registry->foo);
    }

    /**
     * Complex test for __isset
     *
     * @covers \Bluz\Registry\Registry::__isset()
     */
    public function testIsset()
    {
        $this->registry->moo = 'maz';

        $this->assertTrue(isset($this->registry->moo));
        $this->assertFalse(isset($this->registry->boo));
    }

    /**
     * Complex test for __unset
     *
     * @covers \Bluz\Registry\Registry::__unset()
     */
    public function testUnset()
    {
        $this->registry->moo = 'maz';
        unset($this->registry->moo);

        $this->assertNull($this->registry->moo);
    }
}
