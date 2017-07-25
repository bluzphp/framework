<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Validator\Rule;

use Bluz\Tests;
use Bluz\Validator\Rule\BetweenInclusiveRule as Rule;

/**
 * Class BetweenTest
 *
 * @package Bluz\Tests\Validator\Rule
 */
class BetweenInclusiveTest extends Tests\FrameworkTestCase
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
     * @return array
     */
    public function providerForPass() : array
    {
        return array(
            [10, 20, 10],
            [10, 20, 20],
            ['a', 'z', 'z'],
            array(
                new \DateTime('yesterday'),
                new \DateTime('tomorrow'),
                new \DateTime('tomorrow')
            ),
        );
    }

    /**
     * @return array
     */
    public function providerForFail() : array
    {
        return array(
            [0, 1, -1],
            [0, 1, 3],
            ['a', 'j', 'z'],
            array(
                new \DateTime('yesterday'),
                new \DateTime('now'),
                new \DateTime('tomorrow')
            ),
        );
    }
}
