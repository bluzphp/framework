<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Validator\Rule;

use Bluz\Tests;
use Bluz\Validator\Rule\IntegerRule as Rule;

/**
 * Class IntegerTest
 *
 * @package Bluz\Tests\Validator\Rule
 */
class IntegerTest extends Tests\FrameworkTestCase
{
    /**
     * @var Rule
     */
    protected $rule;

    /**
     * Setup validator instance
     */
    protected function setUp()
    {
        $this->rule = new Rule;
    }

    /**
     * @dataProvider providerForPass
     *
     * @param $input
     */
    public function testValidIntegersShouldPass($input)
    {
        self::assertTrue($this->rule->validate($input));
        self::assertNotEmpty($this->rule->__toString());
    }

    /**
     * @dataProvider providerForFail
     *
     * @param $input
     */
    public function testInvalidIntegersShouldFail($input)
    {
        self::assertFalse($this->rule->validate($input));
        self::assertNotEmpty($this->rule->__toString());
    }

    /**
     * @return array
     */
    public function providerForPass() : array
    {
        return array(
            [16],
            ['165'],
            [123456],
            [PHP_INT_MAX],
        );
    }

    /**
     * @return array
     */
    public function providerForFail() : array
    {
        return array(
            [null],
            [''],
            [' '],
            ['a'],
            ['Foo'],
            ['1.44'],
            [1e-5],
        );
    }
}
