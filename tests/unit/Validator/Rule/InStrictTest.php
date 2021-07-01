<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Validator\Rule;

use Bluz\Tests;
use Bluz\Validator\Rule\InStrictRule as Rule;

/**
 * Class InTest
 *
 * @package Bluz\Tests\Validator\Rule
 */
class InStrictTest extends Tests\FrameworkTestCase
{
    /**
     * @dataProvider providerForPass
     *
     * @param      $input
     * @param null $haystack
     */
    public function testSuccessInValidatorCases($input, $haystack = null)
    {
        $rule = new Rule($haystack);
        self::assertTrue($rule->validate($input));
        self::assertNotEmpty($rule->__toString());
    }

    /**
     * @dataProvider providerForFail
     *
     * @param      $input
     * @param null $haystack
     */
    public function testInvalidInValidatorCases($input, $haystack = null)
    {
        $rule = new Rule($haystack);
        self::assertFalse($rule->validate($input));
        self::assertNotEmpty($rule->__toString());
    }

    /**
     * @return array
     */
    public function providerForPass(): array
    {
        return [
            ['foo', 'foo'],
            ['foo', ['foo', 'bar']],
            ['foo', 'barfoobaz'],
            [1, [1, 2, 3]],
            ['1', ['1', 2, 3]],
        ];
    }

    /**
     * @return array
     */
    public function providerForFail(): array
    {
        return [
            ['', 'barfoobaz'],
            ['foo', 'barbazFOO'],
            ['', 42],
            [1, ['1', 2, 3]],
            ['1', [1, 2, 3]],
        ];
    }
}
