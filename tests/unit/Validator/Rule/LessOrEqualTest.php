<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Validator\Rule;

use Bluz\Tests;
use Bluz\Validator\Rule\LessOrEqualRule as Rule;

/**
 * Class MaxTest
 *
 * @package Bluz\Tests\Validator\Rule
 */
class LessOrEqualTest extends Tests\FrameworkTestCase
{
    /**
     * @dataProvider providerForPass
     *
     * @param $maxValue
     * @param $input
     */
    public function testValidLessInputShouldPass($maxValue, $input)
    {
        $rule = new Rule($maxValue);
        self::assertTrue($rule->validate($input));
        self::assertNotEmpty($rule->__toString());
    }

    /**
     * @dataProvider providerForFail
     *
     * @param $maxValue
     * @param $input
     */
    public function testInvalidLessValueShouldFail($maxValue, $input)
    {
        $rule = new Rule($maxValue);
        self::assertFalse($rule->validate($input));
        self::assertNotEmpty($rule->__toString());
    }

    /**
     * @return array
     */
    public function providerForPass() : array
    {
        return [
            [0, ''], // empty string is equal zero
            [1, true],
            [0, false],
            [0, null],
            [0, 0],
            [0, '0'],
            ['1', 0],
        ];
    }

    /**
     * @return array
     */
    public function providerForFail() : array
    {
        return [
            [0, true],
            [0, 1],
        ];
    }
}
