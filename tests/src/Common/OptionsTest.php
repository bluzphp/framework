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
use Bluz\Tests\Common\Fixtures\ConcreteOptions;

/**
 * Tests for Options trait
 *
 * @package  Bluz\Tests\Common
 *
 * @author   Anton Shevchuk
 * @created  23.05.14 11:32
 */
class OptionsTest extends TestCase
{
    /**
     * @var ConcreteOptions
     */
    protected $class;

    /**
     * @var array
     */
    protected $options;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->class = new ConcreteOptions();
        $this->options = ['foo' => 'bar', 'foo bar' => 'qux', 'baz' => ['foo' => 'bar']];
    }

    /**
     * Test setup options
     */
    public function testSetOptions()
    {
        $this->class->setOptions($this->options);

        $this->assertEquals('bar', $this->class->foo);
        $this->assertEquals('qux', $this->class->fooBar);
    }

    /**
     * Test get options
     */
    public function testGetOptions()
    {
        $this->class->setOptions($this->options);

        $this->assertEquals($this->options, $this->class->getOptions());
        $this->assertEquals('bar', $this->class->getOption('foo'));
        $this->assertEquals('bar', $this->class->getOption('baz', 'foo'));
    }

    /**
     * Test get options
     */
    public function testGetFalseOption()
    {
        $this->class->setOptions($this->options);

        $this->assertNull($this->class->getOption('bar'));
        $this->assertNull($this->class->getOption('baz', 'bar'));
    }
}
