<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Validator\Rule;

use Bluz\Tests;
use Bluz\Validator\Rule\ConditionRule as Rule;

/**
 * Class AlphaTest
 *
 * @package Bluz\Tests\Validator\Rule
 */
class ConditionTest extends Tests\FrameworkTestCase
{
    /**
     * @dataProvider providerForPass
     *
     * @param        $condition
     * @param string $input
     */
    public function testValidAlphanumericCharsShouldPass($condition, $input = 'any')
    {
        $rule = new Rule($condition);
        self::assertTrue($rule->validate($input));
        self::assertNotEmpty($rule->__toString());
    }

    /**
     * @dataProvider providerForFail
     *
     * @param        $condition
     * @param string $input
     */
    public function testInvalidAlphanumericCharsShouldFail($condition, $input = 'any')
    {

        $rule = new Rule($condition);
        self::assertFalse($rule->validate($input));
        self::assertNotEmpty($rule->__toString());
    }

    /**
     * @return array
     */
    public function providerForPass() : array
    {
        return [
            [4 > 2, 'always'],
            [is_int(42), 'always'],
        ];
    }

    /**
     * @return array
     */
    public function providerForFail() : array
    {
        return [
            [4 < 2, 'always'],
            [is_int(42.0204), 'always'],
        ];
    }
}
