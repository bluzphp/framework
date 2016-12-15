<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Validator\Rule;

use Bluz\Tests;
use Bluz\Validator\Rule\Condition;

/**
 * Class AlphaTest
 * @package Bluz\Tests\Validator\Rule
 */
class ConditionTest extends Tests\TestCase
{
    /**
     * @dataProvider providerForPass
     * @param $condition
     * @param string $input
     */
    public function testValidAlphanumericCharsShouldReturnTrue($condition, $input = 'any')
    {
        $validator = new Condition($condition);
        self::assertTrue($validator->validate($input));
        self::assertTrue($validator->assert($input));
    }

    /**
     * @dataProvider providerForFail
     * @expectedException \Bluz\Validator\Exception\ValidatorException
     * @param $condition
     * @param string $input
     */
    public function testInvalidAlphanumericCharsShouldReturnFalse($condition, $input = 'any')
    {

        $validator = new Condition($condition);
        self::assertFalse($validator->validate($input));
        self::assertFalse($validator->assert($input));
    }

    /**
     * @return array
     */
    public function providerForPass()
    {
        return array(
            [(4 > 2), 'always'],
            [(is_int(42)), 'always'],
        );
    }

    /**
     * @return array
     */
    public function providerForFail()
    {
        return array(
            [(4 < 2), 'always'],
            [(is_int(42.0204)), 'always'],
        );
    }
}
