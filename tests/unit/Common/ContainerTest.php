<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */

namespace Bluz\Tests\Common;

use Bluz\Tests\FrameworkTestCase;
use Bluz\Tests\Fixtures\Common\ConcreteContainer;

/**
 * Tests for Container traits
 *
 * @package  Bluz\Tests\Common
 *
 * @author   Anton Shevchuk
 * @created  27.10.14 12:31
 */
class ContainerTest extends FrameworkTestCase
{
    /**
     * @var ConcreteContainer
     */
    protected $class;

    protected $example = [
        'foo' => 'bar',
        'quz' => 'qux'
    ];

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->class = new ConcreteContainer();
    }

    /**
     * Test setup Container from array and get as array
     */
    public function testContainerSetterAndGetter()
    {
        $this->class->setFromArray($this->example);

        self::assertEqualsArray($this->example, $this->class->toArray());
    }

    /**
     * Test Reset Container data to null
     */
    public function testResetContainer()
    {
        $this->class->setFromArray($this->example);
        $this->class->resetArray();

        $result = $this->class->toArray();

        self::assertArrayHasKey('foo', $result);
        self::assertArrayHasKey('quz', $result);
        self::assertNull($result['foo']);
        self::assertNull($result['quz']);
    }

    /**
     * Test RegularAccess trait
     */
    public function testRegularAccess()
    {
        $this->class->set('foo', 'bar');
        $this->class->set('quz', 'qux');

        $this->class->delete('quz');

        self::assertEquals('bar', $this->class->get('foo'));
        self::assertFalse($this->class->contains('quz'));
        self::assertFalse($this->class->contains('some other'));
        self::assertNull($this->class->get('quz'));
    }

    /**
     * Test MagicAccess trait
     */
    public function testMagicAccess()
    {
        $this->class->foo = 'bar';
        $this->class->quz = 'qux';

        unset($this->class->quz);

        self::assertEquals('bar', $this->class->foo);
        self::assertFalse(isset($this->class->quz));
        self::assertFalse(isset($this->class->some));
        self::assertTrue(empty($this->class->quz));
        self::assertTrue(empty($this->class->some));
        self::assertNull($this->class->quz);
    }

    /**
     * Test ArrayAccess trait
     */
    public function testArrayAccess()
    {
        $this->class['foo'] = 'bar';
        $this->class['quz'] = 'qux';

        unset($this->class['quz']);

        self::assertEquals('bar', $this->class['foo']);
        self::assertFalse(isset($this->class['quz']));
        self::assertFalse(isset($this->class['some']));
        self::assertTrue(empty($this->class['quz']));
        self::assertTrue(empty($this->class['some']));
        self::assertNull($this->class['quz']);
    }

    /**
     * Test JsonSerialize implementation
     */
    public function testJsonSerialize()
    {
        $this->class->setFromArray($this->example);

        self::assertJsonStringEqualsJsonString(json_encode($this->example), json_encode($this->class));
    }
}
