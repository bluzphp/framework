<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Validator\Rule;

use Bluz\Tests;
use Bluz\Validator\Rule\ContainsRule;

/**
 * Class ContainsTest
 *
 * @package Bluz\Tests\Validator\Rule
 */
class ContainsTest extends Tests\FrameworkTestCase
{
    /**
     * @dataProvider providerForPass
     *
     * @param $start
     * @param $input
     */
    public function testStringsContainingExpectedValueShouldPass($start, $input)
    {
        $validator = new ContainsRule($start);
        self::assertTrue($validator->validate($input));
    }

    /**
     * @dataProvider providerForFail
     *
     * @param      $start
     * @param      $input
     */
    public function testStringsNotContainsExpectedValueShouldFail($start, $input)
    {
        $validator = new ContainsRule($start);
        self::assertFalse($validator->validate($input));
        self::assertNotEmpty($validator->__toString());
    }

    /**
     * @return array
     */
    public function providerForPass(): array
    {
        return [
            ['foo', ['bar', 'foo']],
            ['foo', 'barfoo'],
            ['foo', 'barFOO'],
            ['foo', 'foobazfoo'],
            ['1', [1, 2, 3]],
        ];
    }

    /**
     * @return array
     */
    public function providerForFail(): array
    {
        return [
            ['foo', ''],
            ['foo', 'barf00'],
            ['foo', ['bar', 'f00']],
            ['4', [1, 2, 3]],
        ];
    }
}
