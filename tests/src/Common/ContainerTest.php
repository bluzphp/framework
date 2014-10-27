<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Common;

use Bluz\Tests\TestCase;
use Bluz\Tests\Common\Fixtures\ConcreteContainer;

/**
 * Tests for Container traits
 *
 * @package  Bluz\Tests\Common
 *
 * @author   Anton Shevchuk
 * @created  27.10.14 12:31
 */
class ContainerTest extends TestCase
{
    /**
     * @var ConcreteContainer
     */
    protected $class;

    protected $example = array(
        'foo' => 'bar',
        'quz' => 'qux'
    );

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

        $this->assertEqualsArray($this->example, $this->class->toArray());
    }

    /**
     * Test Reset Container data to null
     */
    public function testResetContainer()
    {
        $this->class->setFromArray($this->example);
        $this->class->resetArray();

        $result = $this->class->toArray();

        $this->assertArrayHasKey('foo', $result);
        $this->assertArrayHasKey('quz', $result);
        $this->assertNull($result['foo']);
        $this->assertNull($result['quz']);
    }

    /**
     * Test RegularAccess trait
     */
    public function testRegularAccess()
    {
        $this->class->set('foo', 'bar');
        $this->class->set('quz', 'qux');

        $this->class->delete('quz');

        $this->assertEquals('bar', $this->class->get('foo'));
        $this->assertFalse($this->class->contains('quz'));
        $this->assertFalse($this->class->contains('some other'));
        $this->assertNull($this->class->get('quz'));
    }

    /**
     * Test MagicAccess trait
     */
    public function testMagicAccess()
    {
        $this->class->foo = 'bar';
        $this->class->quz = 'qux';

        unset($this->class->quz);

        $this->assertEquals('bar', $this->class->foo);
        $this->assertFalse(isset($this->class->quz));
        $this->assertFalse(isset($this->class->some));
        $this->assertTrue(empty($this->class->quz));
        $this->assertTrue(empty($this->class->some));
        $this->assertNull($this->class->quz);
    }

    /**
     * Test ArrayAccess trait
     */
    public function testArrayAccess()
    {
        $this->class['foo'] = 'bar';
        $this->class['quz'] = 'qux';

        unset($this->class['quz']);

        $this->assertEquals('bar', $this->class['foo']);
        $this->assertFalse(isset($this->class['quz']));
        $this->assertFalse(isset($this->class['some']));
        $this->assertTrue(empty($this->class['quz']));
        $this->assertTrue(empty($this->class['some']));
        $this->assertNull($this->class['quz']);
    }

    /**
     * Test JsonSerialize implementation
     */
    public function testJsonSerialize()
    {
        $this->class->setFromArray($this->example);

        $this->assertJsonStringEqualsJsonString(json_encode($this->example), json_encode($this->class));
    }
}
