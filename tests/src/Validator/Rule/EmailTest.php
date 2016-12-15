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
use Bluz\Validator\Rule\Email;

/**
 * Class EmailTest
 * @package Bluz\Tests\Validator\Rule
 */
class EmailTest extends Tests\TestCase
{
    /**
     * @dataProvider providerForPass
     * @param $validEmail
     */
    public function testValidEmailShouldPass($validEmail)
    {
        $validator = new Email();
        self::assertTrue($validator->validate($validEmail));
        self::assertTrue($validator->assert($validEmail));
    }

    /**
     * @dataProvider providerForFail
     * @expectedException \Bluz\Validator\Exception\ValidatorException
     * @param $invalidEmail
     */
    public function testInvalidEmailsShouldFailValidation($invalidEmail)
    {
        $validator = new Email();
        self::assertFalse($validator->validate($invalidEmail));
        self::assertFalse($validator->assert($invalidEmail));
    }

    /**
     * Used small set for testing
     */
//    public function testValidEmailWithDomainCheck()
//    {
//        $validator = new Email(true);
//        self::assertTrue($validator->validate('test@test.com'));
//        self::assertFalse($validator->validate('a@a.a'));
//    }

    /**
     * @return array
     */
    public function providerForPass()
    {
        return array(
            ['test@test.com'],
            ['mail+mail@gmail.com'],
            ['mail.email@e.test.com'],
            ['a@a.a']
        );
    }

    /**
     * @return array
     */
    public function providerForFail()
    {
        return array(
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
        );
    }
}
