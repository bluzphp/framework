<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Validator\Rule;

use Bluz\Tests;
use Bluz\Validator\Rule\ContainsStrictRule as Rule;

/**
 * Class ContainsTest
 *
 * @package Bluz\Tests\Validator\Rule
 */
class ContainsStrictTest extends Tests\FrameworkTestCase
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
    public function testStringsNotContainsExpectedValueShouldFail($start, $input)
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
            ['foo', ['bar', 'foo']],
            ['foo', 'barfoo'],
            [1, [1, 2, 3]],
        ];
    }

    /**
     * @return array
     */
    public function providerForFail(): array
    {
        return [
            ['foo', ['bar', 'Foo']],
            ['foo', 'barFoo'],
            ['1', [1, 2, 3]],
        ];
    }
}
