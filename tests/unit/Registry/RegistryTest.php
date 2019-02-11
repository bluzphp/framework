<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Registry;

use Bluz;
use Bluz\Registry\Registry;
use Bluz\Tests\FrameworkTestCase;

/**
 * RegistryTest
 *
 * @package  Bluz\Tests
 *
 * @author   Anton Shevchuk
 * @created  14.05.2014 11:09
 */
class RegistryTest extends FrameworkTestCase
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

        self::assertEquals('bar', $this->registry->get('foo'));
    }

    /**
     * Complex test for setter/getter
     */
    public function testSetGet()
    {
        self::assertNull($this->registry->get('foo'));

        $this->registry->set('foo', 'baz');

        self::assertEquals('baz', $this->registry->get('foo'));
    }

    /**
     * Complex test for contains registry key
     */
    public function testContains()
    {
        $this->registry->set('moo', 'maz');

        self::assertTrue($this->registry->contains('moo'));
        self::assertFalse($this->registry->contains('boo'));
    }

    /**
     * Complex test for delete registry key
     */
    public function testRemove()
    {
        $this->registry->set('moo', 'maz');
        $this->registry->delete('moo');

        self::assertNull($this->registry->get('moo'));
        self::assertFalse($this->registry->contains('moo'));
    }
}
