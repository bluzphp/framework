<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Unit\Validator\Rule;

use Bluz\Tests\Unit\Unit;
use Bluz\Validator\Rule\MoreOrEqualRule as Rule;

/**
 * Class MinTest
 *
 * @package Bluz\Tests\Validator\Rule
 */
class MoreOrEqualTest extends Unit
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
    public function providerForPass(): array
    {
        return array(
            [1, true],
            [0, false],
            [0, null],
            [0, 0],
            [0, '0'],
            [0, '1'],
        );
    }

    /**
     * @return array
     */
    public function providerForFail(): array
    {
        return array(
            [2, false],
            [2, null],
            [2, ''],
            [2, 0],
        );
    }
}
