<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Validator\Rule;

use Bluz\Tests;
use Bluz\Validator\Rule\EqualsStrictRule as Rule;

/**
 * Class EqualsTest
 *
 * @package Bluz\Tests\Validator\Rule
 */
class EqualsStrictTest extends Tests\FrameworkTestCase
{
    /**
     * @dataProvider providerForPass
     *
     * @param $start
     * @param $input
     */
    public function testStringsContainingExpectedValueShouldPass($start, $input)
    {
        $rule = new Rule($start);
        self::assertTrue($rule->validate($input));
        self::assertNotEmpty($rule->__toString());
    }

    /**
     * @dataProvider providerForFail
     *
     * @param      $start
     * @param      $input
     */
    public function testStringsNotEqualsExpectedValueShouldNotPass($start, $input)
    {
        $rule = new Rule($start);
        self::assertFalse($rule->validate($input));
        self::assertNotEmpty($rule->__toString());
    }

    /**
     * @return array
     */
    public function providerForPass(): array
    {
        return [
            ['', ''],
            ['foo', 'foo'],
            [10, 10],
            ['10', '10'],
        ];
    }

    /**
     * @return array
     */
    public function providerForFail(): array
    {
        return [
            ['', 0],
            ['', null],
            ['', false],
            ['foo', ''],
            ['foo', 'Foo'],
            [10, '10'],
        ];
    }
}
