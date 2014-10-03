<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Common;

use Bluz\Common\Nil;
use Bluz\Tests\TestCase;

/**
 * Tests for Nil
 *
 * @package  Bluz\Tests\Common
 *
 * @author   Anton Shevchuk
 * @created  23.05.14 11:47
 */
class NilTest extends TestCase
{
    /**
     * @var Nil
     */
    protected $class;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->class = new Nil();
    }

    /**
     * Test setup options
     */
    public function testComplexNill()
    {
        // methods
        $this->assertNull(Nil::call());
        $this->assertNull($this->class->call());

        // properties
        $this->class->foo = 'bar';
        $this->assertNull($this->class->foo);

        // magic __toString
        $this->assertEmpty('' . $this->class);
    }
}
