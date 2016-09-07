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
     */
    public function testValidEmailShouldPass($validEmail)
    {
        $validator = new Email();
        $this->assertTrue($validator->validate($validEmail));
        $this->assertTrue($validator->assert($validEmail));
    }

    /**
     * @dataProvider providerForFail
     * @expectedException \Bluz\Validator\Exception\ValidatorException
     */
    public function testInvalidEmailsShouldFailValidation($invalidEmail)
    {
        $validator = new Email();
        $this->assertFalse($validator->validate($invalidEmail));
        $this->assertFalse($validator->assert($invalidEmail));
    }

    /**
     * Used small set for testing
     */
//    public function testValidEmailWithDomainCheck()
//    {
//        $validator = new Email(true);
//        $this->assertTrue($validator->validate('test@test.com'));
//        $this->assertFalse($validator->validate('a@a.a'));
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
