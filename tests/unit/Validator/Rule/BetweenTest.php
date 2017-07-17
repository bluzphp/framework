<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */

namespace Bluz\Tests\Validator\Rule;

use \DateTime;
use Bluz\Tests;
use Bluz\Validator\Rule\Between;

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
     * @param $inclusive
     * @param $input
     */
    public function testValuesBetweenBoundsShouldPass($min, $max, $inclusive, $input)
    {
        $validator = new Between($min, $max, $inclusive);
        self::assertTrue($validator->validate($input));
        self::assertTrue($validator->assert($input));
    }

    /**
     * @dataProvider providerForFail
     * @expectedException \Bluz\Validator\Exception\ValidatorException
     *
     * @param $min
     * @param $max
     * @param $inclusive
     * @param $input
     */
    public function testValuesOutBoundsShouldRaiseException($min, $max, $inclusive, $input)
    {
        $validator = new Between($min, $max, $inclusive);
        self::assertFalse($validator->validate($input));
        self::assertNotEmpty($validator->__toString());
        self::assertFalse($validator->assert($input));
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
        new Between($min, $max);
    }

    /**
     * @return array
     */
    public function providerForPass()
    {
        return array(
            [0, 1, true, 0],
            [0, 1, true, 1],
            [10, 20, false, 15],
            [10, 20, true, 20],
            [-10, 20, false, -5],
            [-10, 20, false, 0],
            ['a', 'z', false, 'j'],
            array(
                new DateTime('yesterday'),
                new DateTime('tomorrow'),
                false,
                new DateTime('now')
            ),
        );
    }

    /**
     * @return array
     */
    public function providerForFail()
    {
        return array(
            [10, 20, true, ''],
            [10, 20, false, ''],
            [0, 1, false, 0],
            [0, 1, false, 1],
            [0, 1, false, 2],
            [0, 1, false, -1],
            [10, 20, false, 999],
            [10, 20, false, 20],
            [-10, 20, false, -11],
            ['a', 'j', false, 'z'],
            array(
                new DateTime('yesterday'),
                new DateTime('now'),
                false,
                new DateTime('tomorrow')
            ),
        );
    }

    /**
     * @return array
     */
    public function providerForComponentException()
    {
        return array(
            [10, 5],
            [10, null],
            [null, 5],
        );
    }
}
