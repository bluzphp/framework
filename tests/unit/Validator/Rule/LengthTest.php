<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Validator\Rule;

use Bluz\Tests;
use Bluz\Validator\Rule\LengthRule as Rule;

/**
 * Class LengthTest
 *
 * @package Bluz\Tests\Validator\Rule
 */
class LengthTest extends Tests\FrameworkTestCase
{
    /**
     * @dataProvider providerForPass
     *
     * @param $string
     * @param $min
     * @param $max
     */
    public function testLengthInsideBoundsShouldPass($string, $min, $max)
    {
        $rule = new Rule($min, $max);
        self::assertTrue($rule->validate($string));
        self::assertNotEmpty($rule->__toString());
    }

    /**
     * @dataProvider providerForFail
     *
     * @param $string
     * @param $min
     * @param $max
     */
    public function testLengthOutsideValidBoundsShouldFail($string, $min, $max)
    {
        $rule = new Rule($min, $max);
        self::assertFalse($rule->validate($string));
        self::assertNotEmpty($rule->__toString());
    }

    /**
     * @dataProvider providerForComponentException
     * @expectedException \Bluz\Validator\Exception\ComponentException
     *
     * @param $min
     * @param $max
     */
    public function testInvalidConstructorParametersShouldThrowComponentExceptionUponInstantiation($min, $max)
    {
        new Rule($min, $max);
    }

    /**
     * @return array
     */
    public function providerForPass(): array
    {
        return array(
            ['foobar', 1, 15],
            ['ççççç', 4, 6],
            [range(1, 20), 1, 30],
            [(object)['foo' => 'bar', 'bar' => 'baz'], 1, 2],
            ['foobar', 1, null], //null is a valid max length, means "no maximum",
            ['foobar', null, 15] //null is a valid min length, means "no minimum"
        );
    }

    /**
     * @return array
     */
    public function providerForFail(): array
    {
        return array(
            [0, 1, 3],
            ['foobar', 1, 3],
            [(object)['foo' => 'bar', 'bar' => 'baz'], 3, 5],
            [range(1, 50), 1, 30],
        );
    }

    /**
     * @return array
     */
    public function providerForComponentException(): array
    {
        return [
            ['a', 15],
            [1, 'abc d'],
            [10, 1],
        ];
    }
}
