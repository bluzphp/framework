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
     * Setup Data Test
     */
    public function testSetFromArray()
    {
        $data = ['foo' => 'bar'];

        $this->registry->setFromArray($data);

        $this->assertEquals('bar', $this->registry->get('foo'));
    }

    /**
     * Complex test for setter/getter
     */
    public function testSetGet()
    {
        $this->assertNull($this->registry->get('foo'));

        $this->registry->set('foo', 'baz');

        $this->assertEquals('baz', $this->registry->get('foo'));
    }

    /**
     * Complex test for contains registry key
     */
    public function testContains()
    {
        $this->registry->set('moo', 'maz');

        $this->assertTrue($this->registry->contains('moo'));
        $this->assertFalse($this->registry->contains('boo'));
    }

    /**
     * Complex test for delete registry key
     */
    public function testRemove()
    {
        $this->registry->set('moo', 'maz');
        $this->registry->delete('moo');

        $this->assertNull($this->registry->get('moo'));
        $this->assertFalse($this->registry->contains('moo'));
    }
}
