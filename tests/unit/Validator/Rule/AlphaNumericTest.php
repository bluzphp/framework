<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Validator\Rule;

use Bluz\Tests;
use Bluz\Validator\Exception\ComponentException;
use Bluz\Validator\Rule\AlphaNumericRule as Rule;

/**
 * Class AlphaNumericTest
 *
 * @package Bluz\Tests\Validator\Rule
 */
class AlphaNumericTest extends Tests\FrameworkTestCase
{
    /**
     * @dataProvider providerForPass
     *
     * @param $validAlphaNumeric
     * @param $additional
     */
    public function testValidAlphaNumericCharsShouldPass($validAlphaNumeric, $additional)
    {
        $rule = new Rule($additional);
        self::assertTrue($rule->validate($validAlphaNumeric));
        self::assertNotEmpty($rule->__toString());
    }

    /**
     * @dataProvider providerForFail
     *
     * @param $invalidAlphaNumeric
     * @param $additional
     */
    public function testInvalidAlphaNumericCharsShouldFail($invalidAlphaNumeric, $additional)
    {
        $rule = new Rule($additional);
        self::assertFalse($rule->validate($invalidAlphaNumeric));
        self::assertNotEmpty($rule->__toString());
    }

    /**
     * @dataProvider providerAdditionalChars
     *
     * @param $additional
     * @param $query
     */
    public function testAdditionalCharsShouldBeRespected($additional, $query)
    {
        $rule = new Rule($additional);
        self::assertTrue($rule->validate($query));
    }

    /**
     * Check templates
     */
    public function testTemplates()
    {
        $rule = new Rule();
        self::assertNotEmpty($rule->__toString());

        $rule = new Rule('[]');
        self::assertNotEmpty($rule->__toString());
    }

    /**
     * @return array
     */
    public function providerForPass(): array
    {
        return [
            ['', ''],
            ['foobar', ''],
            ['foobar', 'foobar'],
            ['0alg-anet0', '0-9'],
            ['1', ''],
            ["\t", ''],
            ["\n", ''],
            ['a', ''],
            ['foobar', ''],
            ['rubinho_', '_'],
            ['google.com', '.'],
            ['foobar foobar', ''],
            ["\nabc", ''],
            ["\tdef", ''],
            ["\nabc \t", ''],
            [0, ''],
        ];
    }

    /**
     * @return array
     */
    public function providerForFail(): array
    {
        return [
            ['@#$', ''],
            ['_', ''],
            ['dgç', ''],
            [1e21, ''], //evaluates to "1.0E+21"
            [null, ''],
            [new \stdClass(), ''],
            [[], ''],
        ];
    }

    /**
     * @return array
     */
    public function providerAdditionalChars(): array
    {
        return [
            ['!@#$%^&*(){}', '!@#$%^&*(){} abc 123'],
            ['[]?+=/\\-_|"\',<>.', "[]?+=/\\-_|\"',<>. \t \n abc 123"],
        ];
    }
}
