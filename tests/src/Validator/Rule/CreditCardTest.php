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
use Bluz\Validator\Rule\CreditCard;

/**
 * Class CreditCardTest
 * @package Bluz\Tests\Validator\Rule
 */
class CreditCardTest extends Tests\TestCase
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
     */
    public function testValidCreditCardsShouldReturnTrue($input)
    {
        $this->assertTrue($this->validator->validate($input));
        $this->assertTrue($this->validator->assert($input));
    }

    /**
     * @dataProvider providerForFail
     * @expectedException \Bluz\Validator\Exception\ValidatorException
     */
    public function testInvalidCreditCardsShouldThrowCreditCardException($input)
    {
        $this->assertFalse($this->validator->validate($input));
        $this->assertFalse($this->validator->assert($input));
    }

    /**
     * @return array
     */
    public function providerForPass()
    {
        return array(
            array('5376 7473 9720 8720'), // MasterCard
            array('4024.0071.5336.1885'), // Visa 16
            array('4024 007 193 879'),    // Visa 13
            array('340-3161-9380-9364'),  // AmericanExpress
            array('30351042633884'),      // Dinners
        );
    }

    /**
     * @return array
     */
    public function providerForFail()
    {
        return array(
            array(null),
            array(''),
            array('it isnt my credit card number'),
            array('&stR@ng3|) (|-|@r$'),
            array('1234 1234 1234 1234'),
            array('1234.1234.1234.1234'),
        );
    }
}
