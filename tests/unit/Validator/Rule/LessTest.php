<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Validator\Rule;

use Bluz\Tests;
use Bluz\Validator\Rule\LessRule as Rule;

/**
 * Class MaxTest
 *
 * @package Bluz\Tests\Validator\Rule
 */
class LessTest extends Tests\FrameworkTestCase
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
    public function providerForPass(): array
    {
        return array(
            [200, ''], // empty string is equal zero
            [200, -200],
            [200, 0],
            [200, 165.0],
        );
    }

    /**
     * @return array
     */
    public function providerForFail(): array
    {
        return [
            [-200, 0],
            [0, 0],
            [200, 250],
        ];
    }
}
