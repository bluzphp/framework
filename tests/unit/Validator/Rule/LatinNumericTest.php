<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Validator\Rule;

use Bluz\Tests;
use Bluz\Validator\Exception\ComponentException;
use Bluz\Validator\Rule\LatinNumericRule as Rule;

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
    public function testValidAlphanumericCharsShouldPass($validAlpha, $additional = '')
    {
        $rule = new Rule($additional);
        self::assertTrue($rule->validate($validAlpha));
        self::assertNotEmpty($rule->__toString());
    }

    /**
     * @dataProvider providerForFail
     *
     * @param        $invalidAlpha
     * @param string $additional
     */
    public function testInvalidAlphanumericCharsShouldFail($invalidAlpha, $additional = '')
    {
        $rule = new Rule($additional);
        self::assertFalse($rule->validate($invalidAlpha));
        self::assertNotEmpty($rule->__toString());
    }

    /**
     * @dataProvider providerForComponentException
     *
     * @param $additional
     */
    public function testInvalidConstructorParamsShouldThrowComponentException($additional)
    {
        $this->expectException(ComponentException::class);
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
     * Check messages
     */
    public function testRuleDescriptionShouldBePresent()
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
            [0],
            [123456789],
            ['1234567890'],
            ['foobar'],
            ['foobar1234567890'],
            ['foobar_', '_'],
            ['google.com.ua', '.'],
            ['foobar foobar', ' ']
        ];
    }

    /**
     * @return array
     */
    public function providerForFail(): array
    {
        return [
            [1e21],
            ['@#$'],
            ['_'],
            ['dgç'],
            [null],
            [new \stdClass()],
            [[]],
        ];
    }

    /**
     * @return array
     */
    public function providerForComponentException(): array
    {
        return [
            [new \stdClass()],
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
            ['!@#$%^&*(){} ', '!@#$%^&*(){} abc'],
            ['[]?+=/\\-_|"\',<>. ', "[]?+=/\\-_|\"',<>. abc"],
        ];
    }
}
