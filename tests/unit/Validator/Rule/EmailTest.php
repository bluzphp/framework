<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Validator\Rule;

use Bluz\Tests;
use Bluz\Validator\Rule\EmailRule as Rule;

/**
 * Class EmailTest
 *
 * @package Bluz\Tests\Validator\Rule
 */
class EmailTest extends Tests\FrameworkTestCase
{
    /**
     * @dataProvider providerForPass
     *
     * @param $validEmail
     */
    public function testValidEmailShouldPassValidation($validEmail)
    {
        $rule = new Rule();
        self::assertTrue($rule->validate($validEmail));
        self::assertNotEmpty($rule->__toString());
    }

    /**
     * @dataProvider providerForFail
     *
     * @param $invalidEmail
     */
    public function testInvalidEmailsShouldFailValidation($invalidEmail)
    {
        $rule = new Rule();
        self::assertFalse($rule->validate($invalidEmail));
        self::assertNotEmpty($rule->__toString());
    }

    /**
     * Used small set for testing
     */
    public function testValidEmailWithDomainCheck()
    {
        self::markTestIncomplete('To slow to check it every time');

        return;
        $validator = new Rule(true);
        self::assertTrue($validator->validate('test@test.com'));
        self::assertFalse($validator->validate('a@a.a'));
    }

    /**
     * @return array
     */
    public function providerForPass() : array
    {
        return [
            ['test@test.com'],
            ['mail+mail@gmail.com'],
            ['mail.email@e.test.com'],
            ['a@a.a']
        ];
    }

    /**
     * @return array
     */
    public function providerForFail() : array
    {
        return [
            ['test@test'],
            ['test'],
            ['test@тест.рф'],
            ['@test.com'],
            ['mail@test@test.com'],
            ['test.test@'],
            ['test.@test.com'],
            ['test@.test.com'],
            ['test@test..com'],
            ['test@test.com.'],
            ['.test@test.com']
        ];
    }
}
