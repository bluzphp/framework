<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Validator\Rule;

use Bluz\Tests;
use Bluz\Validator\Rule\AlphaRule as Rule;

/**
 * Class AlphaTest
 *
 * @package Bluz\Tests\Validator\Rule
 */
class AlphaTest extends Tests\FrameworkTestCase
{
    /**
     * @dataProvider providerForPass
     *
     * @param $validAlpha
     * @param $additional
     */
    public function testValidAlphanumericCharsShouldPass($validAlpha, $additional)
    {
        $rule = new Rule($additional);
        self::assertTrue($rule->validate($validAlpha));
        self::assertNotEmpty($rule->__toString());
    }

    /**
     * @dataProvider providerForFail
     *
     * @param $invalidAlpha
     * @param $additional
     */
    public function testInvalidAlphanumericCharsShouldFail($invalidAlpha, $additional)
    {
        $rule = new Rule($additional);
        self::assertFalse($rule->validate($invalidAlpha));
        self::assertNotEmpty($rule->__toString());
    }

    /**
     * @dataProvider providerForComponentException
     * @expectedException \Bluz\Validator\Exception\ComponentException
     *
     * @param $additional
     */
    public function testInvalidConstructorParamsShouldRaiseComponentException($additional)
    {
        new Rule($additional);
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
        self::assertNotEmpty($rule->__toString());
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
    public function providerForPass()
    {
        return [
            ['', ''],
            ['foobar', ''],
            ['foobar', 'foobar'],
            ['0alg-anet0', '0-9'],
            ['a', ''],
            ["\t", ''],
            ["\n", ''],
            ['foobar', ''],
            ['python_', '_'],
            ['google.com.ua', '.'],
            ['foobar foobar', ''],
            ["\nabc", ''],
            ["\tdef", ''],
            ["\nabc \t", ''],
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
            ['122al', ''],
            ['122', ''],
            [11123, ''],
            [1e21, ''],
            [0, ''],
            [null, ''],
            [new \stdClass, ''],
            [[], ''],
        ];
    }

    /**
     * @return array
     */
    public function providerForComponentException(): array
    {
        return [
            [new \stdClass],
            [[]],
            [0x2]
        ];
    }

    /**
     * @return array
     */
    public function providerAdditionalChars(): array
    {
        return [
            ['!@#$%^&*(){}', '!@#$%^&*(){} abc'],
            ['[]?+=/\\-_|"\',<>.', "[]?+=/\\-_|\"',<>. \t \n abc"],
        ];
    }
}
