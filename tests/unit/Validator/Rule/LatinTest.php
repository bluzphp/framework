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
use Bluz\Validator\Rule\Latin;

/**
 * Class AlphaTest
 *
 * @package Bluz\Tests\Validator\Rule
 */
class LatinTest extends Tests\FrameworkTestCase
{
    /**
     * @dataProvider providerForPass
     *
     * @param        $validAlpha
     * @param string $additional
     */
    public function testValidAlphanumericCharsShouldReturnTrue($validAlpha, $additional = '')
    {
        $validator = new Latin($additional);
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
        $validator = new Latin($additional);
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
        new Latin($additional);
    }

    /**
     * @dataProvider providerAdditionalChars
     *
     * @param $additional
     * @param $query
     */
    public function testAdditionalCharsShouldBeRespected($additional, $query)
    {
        $validator = new Latin($additional);
        self::assertTrue($validator->validate($query));
    }

    /**
     * Check templates
     */
    public function testTemplates()
    {
        $validator = new Latin();
        self::assertNotEmpty($validator->__toString());

        $validator = new Latin('[]');
        self::assertNotEmpty($validator->__toString());
    }

    /**
     * @return array
     */
    public function providerForPass()
    {
        return array(
            ['foobar'],
            ['foobar', 'foobar'],
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
            ['@#$'],
            ['_'],
            ['dgç'],
            ['122al'],
            ['122'],
            [11123],
            [1e21],
            [0],
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
