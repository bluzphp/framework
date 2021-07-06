<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Validator\Rule;

use Bluz\Tests;
use Bluz\Validator\Rule\EqualsRule as Rule;

/**
 * Class EqualsTest
 *
 * @package Bluz\Tests\Validator\Rule
 */
class EqualsTest extends Tests\FrameworkTestCase
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
            ['', null],
            ['', false],
            ['', ''],
            ['foo', 'foo'],
            [10, '10'],
        ];
    }

    /**
     * @return array
     */
    public function providerForFail(): array
    {
        return [
            ['foo', ''],
            ['foo', 'bar'],
        ];
    }
}
