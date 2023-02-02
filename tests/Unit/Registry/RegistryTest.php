<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Unit\Registry;

use Bluz;
use Bluz\Registry\Registry;
use Bluz\Tests\Unit\Unit;

/**
 * RegistryTest
 *
 * @package  Bluz\Tests
 *
 * @author   Anton Shevchuk
 * @created  14.05.2014 11:09
 */
class RegistryTest extends Unit
{
    /**
     * @var Registry
     */
    protected Registry $registry;

    /**
     * setUp
     */
    public function setUp(): void
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

        self::assertTrue($this->registry->has('moo'));
        self::assertFalse($this->registry->has('boo'));
    }

    /**
     * Complex test for delete registry key
     */
    public function testRemove()
    {
        $this->registry->set('moo', 'maz');
        $this->registry->delete('moo');

        self::assertNull($this->registry->get('moo'));
        self::assertFalse($this->registry->has('moo'));
    }
}
