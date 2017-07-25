<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Validator\Rule;

use Bluz\Tests;
use Bluz\Validator\Rule\MoreRule as Rule;

/**
 * Class MinTest
 *
 * @package Bluz\Tests\Validator\Rule
 */
class MoreTest extends Tests\FrameworkTestCase
{
    /**
     * @dataProvider providerForPass
     *
     * @param $minValue
     * @param $input
     */
    public function testValidMoreShouldPass($minValue, $input)
    {
        $rule = new Rule($minValue);
        self::assertTrue($rule->validate($input));
        self::assertNotEmpty($rule->__toString());
    }

    /**
     * @dataProvider providerForFail
     *
     * @param $minValue
     * @param $input
     */
    public function testInvalidMoreShouldFail($minValue, $input)
    {
        $rule = new Rule($minValue);
        self::assertFalse($rule->validate($input));
        self::assertNotEmpty($rule->__toString());
    }

    /**
     * @return array
     */
    public function providerForPass() : array
    {
        return array(
            [-1, ''],       // empty string is equal zero
            [0, 100],
            [0, 123.0],
            [-50, 0],
        );
    }

    /**
     * @return array
     */
    public function providerForFail() : array
    {
        return array(
            [0, ''],     // empty string is equal zero
            [100, ''],
            [100, 0],
            [0, -50],
            [0, 0],
        );
    }
}
