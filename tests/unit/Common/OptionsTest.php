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
use Bluz\Tests\Fixtures\Common\ConcreteOptions;

/**
 * Tests for Options trait
 *
 * @package  Bluz\Tests\Common
 *
 * @author   Anton Shevchuk
 * @created  23.05.14 11:32
 */
class OptionsTest extends FrameworkTestCase
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
        $this->options = ['foo' => 'bar', 'foo bar' => 'qux', 'baz' => ['foo' => 'bar'], 'moo' => 'Moo'];
    }

    /**
     * Test setup options
     */
    public function testSetOptions()
    {
        $this->class->setOptions($this->options);

        self::assertEquals('bar', $this->class->foo);
        self::assertEquals('qux', $this->class->fooBar);
    }

    /**
     * Test get options
     */
    public function testGetOptions()
    {
        $this->class->setOptions($this->options);

        self::assertEquals($this->options, $this->class->getOptions());
        self::assertEquals('bar', $this->class->getOption('foo'));
        self::assertEquals('bar', $this->class->getOption('baz', 'foo'));
        self::assertEquals('Moo-Moo', $this->class->getOption('moo'));
    }

    /**
     * Test get options
     */
    public function testGetFalseOption()
    {
        $this->class->setOptions($this->options);

        self::assertNull($this->class->getOption('bar'));
        self::assertNull($this->class->getOption('baz', 'bar'));
    }
}
