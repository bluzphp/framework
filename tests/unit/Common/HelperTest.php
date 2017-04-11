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
use Bluz\Tests\Fixtures\Common\ConcreteHelpers;

/**
 * Tests for Helper trait
 *
 * @package  Bluz\Tests\Common
 *
 * @author   Anton Shevchuk
 * @created  14.01.14 11:47
 */
class HelperTest extends TestCase
{
    const MAGIC_NUMBER = 42;

    /**
     * @var ConcreteHelpers
     */
    protected $class;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->class = new ConcreteHelpers();
    }

    /**
     * Helper paths is not initialized, and helper file not found
     * @expectedException \Bluz\Common\Exception\CommonException
     */
    public function testInvalidHelperCall()
    {
        $this->class->helperFunction(self::MAGIC_NUMBER);
    }

    /**
     * Helper path initialized, but file consists some stuff, it's not callable
     * @expectedException \Bluz\Common\Exception\CommonException
     */
    public function testInvalidHelperCall2()
    {
        $this->class->addHelperPath(__DIR__ .'/Fixtures/Helper');
        $this->class->helperInvalidFunction(self::MAGIC_NUMBER);
    }

    /**
     * complex test
     * test Add Helper Path
     * test call Function helper
     * test call Class helper
     */
    public function testAddHelperPath()
    {
        $this->class->addHelperPath(__DIR__ .'/Fixtures/Helper');
        $this->class->addHelperPath(__DIR__ .'/Fixtures/Helper2');
        self::assertEquals(
            $this->class->helperFunction(self::MAGIC_NUMBER),
            self::MAGIC_NUMBER
        );
        self::assertEquals(
            $this->class->helper2Function(self::MAGIC_NUMBER),
            self::MAGIC_NUMBER
        );
    }

    /**
     * test Set Helper Path
     */
    public function testSetHelperPaths()
    {
        $this->class->setHelpersPath([__DIR__ .'/Fixtures/Helper', __DIR__ .'/Fixtures/Helper2']);
        self::assertEquals(
            $this->class->helperFunction(self::MAGIC_NUMBER),
            self::MAGIC_NUMBER
        );
        self::assertEquals(
            $this->class->helper2Function(self::MAGIC_NUMBER),
            self::MAGIC_NUMBER
        );
    }
}
