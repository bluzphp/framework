<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Validator\Rule;

use Bluz\Tests;
use Bluz\Validator\Rule\Min;

/**
 * Class MinTest
 * @package Bluz\Tests\Validator\Rule
 */
class MinTest extends Tests\TestCase
{
    /**
     * @dataProvider providerForPass
     * @param $minValue
     * @param $inclusive
     * @param $input
     */
    public function testValidMinShouldReturnTrue($minValue, $inclusive, $input)
    {
        $min = new Min($minValue, $inclusive);
        self::assertTrue($min->validate($input));
        self::assertNotEmpty($min->__toString());
    }

    /**
     * @dataProvider providerForFail
     * @param $minValue
     * @param $inclusive
     * @param $input
     */
    public function testInvalidMinShouldReturnFalse($minValue, $inclusive, $input)
    {
        $min = new Min($minValue, $inclusive);
        self::assertFalse($min->validate($input));
    }

    /**
     * @return array
     */
    public function providerForPass()
    {
        return array(
            [0, true, ''],       // empty string is equal zero
            [100, false, 123.0],
            [100, true, 100],
            [-50, true, -50],
            [-50, false, 100],
            [100, false, 200],
        );
    }

    /**
     * @return array
     */
    public function providerForFail()
    {
        return array(
            [0, false, ''],     // empty string is equal zero
            [100, false, ''],
            [100, false, 50],
            [0, false, -50],
            [50, false, 50],
        );
    }
}
