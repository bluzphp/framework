<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Validator\Rule;

use Bluz\Tests;
use Bluz\Validator\Rule\BetweenRule as Rule;

/**
 * Class BetweenTest
 *
 * @package Bluz\Tests\Validator\Rule
 */
class BetweenTest extends Tests\FrameworkTestCase
{
    /**
     * @dataProvider providerForPass
     *
     * @param $min
     * @param $max
     * @param $input
     */
    public function testValuesBetweenBoundsShouldPass($min, $max, $input)
    {
        $rule = new Rule($min, $max);
        self::assertTrue($rule->validate($input));
        self::assertNotEmpty($rule->__toString());
    }

    /**
     * @dataProvider providerForFail
     *
     * @param $min
     * @param $max
     * @param $input
     */
    public function testValuesOutBoundsShouldFail($min, $max, $input)
    {
        $rule = new Rule($min, $max);
        self::assertFalse($rule->validate($input));
        self::assertNotEmpty($rule->__toString());
    }

    /**
     * @dataProvider providerForComponentException
     * @expectedException \Bluz\Validator\Exception\ComponentException
     *
     * @param $min
     * @param $max
     */
    public function testInvalidConstructionParamsShouldRaiseException($min, $max)
    {
        new Rule($min, $max);
    }

    /**
     * @return array
     */
    public function providerForPass() : array
    {
        return [
            [10, 20, 11],
            [10, 20, 19],
            [-10, 20, -5],
            [-10, 20, 0],
            ['a', 'z', 'j'],
            [
                new \DateTime('yesterday'),
                new \DateTime('tomorrow'),
                new \DateTime('now')
            ],
        ];
    }

    /**
     * @return array
     */
    public function providerForFail() : array
    {
        return [
            [0, 1, -1],
            [0, 1, 0],
            [0, 1, 1],
            [0, 1, 3],
            [10, 20, ''],
            [10, 20, 999],
            [10, 20, 20],
            [-10, 20, -11],
            ['a', 'j', 'z'],
            [
                new \DateTime('yesterday'),
                new \DateTime('now'),
                new \DateTime('tomorrow')
            ],
        ];
    }

    /**
     * @return array
     */
    public function providerForComponentException() : array
    {
        return [
            [10, 5],
            [10, null],
            [null, 5],
        ];
    }
}
