<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Common;

use Bluz\Common\Nil;
use Bluz\Tests\FrameworkTestCase;

/**
 * Tests for Nil
 *
 * @package  Bluz\Tests\Common
 *
 * @author   Anton Shevchuk
 * @created  23.05.14 11:47
 */
class NilTest extends FrameworkTestCase
{
    /**
     * @var Nil
     */
    protected $nil;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->nil = new Nil();
    }

    /**
     * Test setup options
     */
    public function testComplexNill()
    {
        // methods
        self::assertNull(Nil::call());
        self::assertNull($this->nil->call());

        // properties
        $this->nil->foo = 'bar';
        self::assertNull($this->nil->foo);
        self::assertFalse(isset($this->nil->foo));

        // magic __toString
        self::assertEmpty('' . $this->nil);
    }
}
