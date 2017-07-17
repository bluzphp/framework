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
use Bluz\Validator\Rule\CreditCard;

/**
 * Class CreditCardTest
 *
 * @package Bluz\Tests\Validator\Rule
 */
class CreditCardTest extends Tests\FrameworkTestCase
{
    /**
     * @var CreditCard
     */
    protected $validator;

    /**
     * Setup validator instance
     */
    protected function setUp()
    {
        $this->validator = new CreditCard;
    }

    /**
     * @dataProvider providerForPass
     *
     * @param $input
     */
    public function testValidCreditCardsShouldReturnTrue($input)
    {
        self::assertTrue($this->validator->validate($input));
        self::assertTrue($this->validator->assert($input));
    }

    /**
     * @dataProvider providerForFail
     * @expectedException \Bluz\Validator\Exception\ValidatorException
     *
     * @param $input
     */
    public function testInvalidCreditCardsShouldThrowCreditCardException($input)
    {
        self::assertFalse($this->validator->validate($input));
        self::assertFalse($this->validator->assert($input));
    }

    /**
     * @return array
     */
    public function providerForPass()
    {
        return array(
            ['5376 7473 9720 8720'], // MasterCard
            ['4024.0071.5336.1885'], // Visa 16
            ['4024 007 193 879'],    // Visa 13
            ['340-3161-9380-9364'],  // AmericanExpress
            ['30351042633884'],      // Dinners
        );
    }

    /**
     * @return array
     */
    public function providerForFail()
    {
        return array(
            [null],
            [''],
            ['it isnt my credit card number'],
            ['&stR@ng3|) (|-|@r$'],
            ['1234 1234 1234 1234'],
            ['1234.1234.1234.1234'],
        );
    }
}
