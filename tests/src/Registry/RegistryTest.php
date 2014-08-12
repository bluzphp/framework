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
     *
     * @return void
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
     * @return void
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
     * @return void
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
     * @return void
     */
    public function testIsset()
    {
        $this->registry->moo = 'maz';

        $this->assertTrue(isset($this->registry->moo));
        $this->assertFalse(isset($this->registry->boo));
    }
}
