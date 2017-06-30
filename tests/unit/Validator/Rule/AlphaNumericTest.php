<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */

namespace Bluz\Tests\Validator\Rule;

use Bluz\Tests;
use Bluz\Validator\Rule\AlphaNumeric;

/**
 * Class AlphaNumericTest
 *
 * @package Bluz\Tests\Validator\Rule
 */
class AlphaNumericTest extends Tests\TestCase
{
    /**
     * @dataProvider providerForPass
     *
     * @param $validAlphaNumeric
     * @param $additional
     */
    public function testValidAlphaNumericCharsShouldReturnTrue($validAlphaNumeric, $additional)
    {
        $validator = new AlphaNumeric($additional);
        self::assertTrue($validator->validate($validAlphaNumeric));
        self::assertTrue($validator->assert($validAlphaNumeric));
    }

    /**
     * @dataProvider providerForFail
     * @expectedException \Bluz\Validator\Exception\ValidatorException
     *
     * @param $invalidAlphaNumeric
     * @param $additional
     */
    public function testInvalidAlphaNumericCharsShouldReturnFalse($invalidAlphaNumeric, $additional)
    {
        $validator = new AlphaNumeric($additional);
        self::assertFalse($validator->validate($invalidAlphaNumeric));
        self::assertFalse($validator->assert($invalidAlphaNumeric));
    }

    /**
     * @dataProvider providerForComponentException
     * @expectedException \Bluz\Validator\Exception\ComponentException
     *
     * @param $additional
     */
    public function testInvalidConstructorParamsShouldThrowComponentException($additional)
    {
        new AlphaNumeric($additional);
    }

    /**
     * @dataProvider providerAdditionalChars
     *
     * @param $additional
     * @param $query
     */
    public function testAdditionalCharsShouldBeRespected($additional, $query)
    {
        $validator = new AlphaNumeric($additional);
        self::assertTrue($validator->validate($query));
    }

    /**
     * Check templates
     */
    public function testTemplates()
    {
        $validator = new AlphaNumeric();
        self::assertNotEmpty($validator->__toString());

        $validator = new AlphaNumeric('[]');
        self::assertNotEmpty($validator->__toString());
    }

    /**
     * @return array
     */
    public function providerForPass()
    {
        return array(
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
        );
    }

    /**
     * @return array
     */
    public function providerForFail()
    {
        return array(
            ['@#$', ''],
            ['_', ''],
            ['dgç', ''],
            [1e21, ''], //evaluates to "1.0E+21"
            [null, ''],
            [new \stdClass, ''],
            [[], ''],
        );
    }

    /**
     * @return array
     */
    public function providerForComponentException()
    {
        return array(
            [new \stdClass],
            [[]],
            [0x2]
        );
    }

    /**
     * @return array
     */
    public function providerAdditionalChars()
    {
        return array(
            ['!@#$%^&*(){}', '!@#$%^&*(){} abc 123'],
            ['[]?+=/\\-_|"\',<>.', "[]?+=/\\-_|\"',<>. \t \n abc 123"],
        );
    }
}
