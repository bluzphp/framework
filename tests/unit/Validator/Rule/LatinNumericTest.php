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
use Bluz\Validator\Rule\LatinNumeric;

/**
 * Class AlphaTest
 *
 * @package Bluz\Tests\Validator\Rule
 */
class LatinNumericTest extends Tests\FrameworkTestCase
{
    /**
     * @dataProvider providerForPass
     *
     * @param        $validAlpha
     * @param string $additional
     */
    public function testValidAlphanumericCharsShouldReturnTrue($validAlpha, $additional = '')
    {
        $validator = new LatinNumeric($additional);
        self::assertTrue($validator->validate($validAlpha));
        self::assertTrue($validator->assert($validAlpha));
    }

    /**
     * @dataProvider providerForFail
     * @expectedException \Bluz\Validator\Exception\ValidatorException
     *
     * @param        $invalidAlpha
     * @param string $additional
     */
    public function testInvalidAlphanumericCharsShouldReturnFalse($invalidAlpha, $additional = '')
    {
        $validator = new LatinNumeric($additional);
        self::assertFalse($validator->validate($invalidAlpha));
        self::assertFalse($validator->assert($invalidAlpha));
    }

    /**
     * @dataProvider providerForComponentException
     * @expectedException \Bluz\Validator\Exception\ComponentException
     *
     * @param $additional
     */
    public function testInvalidConstructorParamsShouldThrowComponentException($additional)
    {
        new LatinNumeric($additional);
    }

    /**
     * @dataProvider providerAdditionalChars
     *
     * @param $additional
     * @param $query
     */
    public function testAdditionalCharsShouldBeRespected($additional, $query)
    {
        $validator = new LatinNumeric($additional);
        self::assertTrue($validator->validate($query));
    }

    /**
     * Check templates
     */
    public function testTemplates()
    {
        $validator = new LatinNumeric();
        self::assertNotEmpty($validator->__toString());

        $validator = new LatinNumeric('[]');
        self::assertNotEmpty($validator->__toString());
    }

    /**
     * @return array
     */
    public function providerForPass()
    {
        return array(
            [0],
            [123456789],
            ['1234567890'],
            ['foobar'],
            ['foobar1234567890'],
            ['foobar_', '_'],
            ['google.com.ua', '.'],
            ['foobar foobar', ' ']
        );
    }

    /**
     * @return array
     */
    public function providerForFail()
    {
        return array(
            [1e21],
            ['@#$'],
            ['_'],
            ['dgç'],
            [null],
            [new \stdClass],
            [[]],
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
            ['!@#$%^&*(){} ', '!@#$%^&*(){} abc'],
            ['[]?+=/\\-_|"\',<>. ', "[]?+=/\\-_|\"',<>. abc"],
        );
    }
}
