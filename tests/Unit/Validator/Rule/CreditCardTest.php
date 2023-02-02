<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Unit\Validator\Rule;

use Bluz\Tests\Unit\Unit;
use Bluz\Validator\Rule\CreditCardRule as Rule;

/**
 * Class CreditCardTest
 *
 * @package Bluz\Tests\Validator\Rule
 */
class CreditCardTest extends Unit
{
    /**
     * @var Rule
     */
    protected $rule;

    /**
     * Setup validator instance
     */
    protected function setUp(): void
    {
        $this->rule = new Rule();
    }

    /**
     * @dataProvider providerForPass
     *
     * @param $input
     */
    public function testValidCreditCardsShouldPass($input)
    {
        self::assertTrue($this->rule->validate($input));
        self::assertNotEmpty($this->rule->__toString());
    }

    /**
     * @dataProvider providerForFail
     *
     * @param $input
     */
    public function testInvalidCreditCardsShouldFail($input)
    {
        self::assertFalse($this->rule->validate($input));
        self::assertNotEmpty($this->rule->__toString());
    }

    /**
     * @return array
     */
    public function providerForPass(): array
    {
        return [
            ['5376 7473 9720 8720'], // MasterCard
            ['4024.0071.5336.1885'], // Visa 16
            ['4024 007 193 879'],    // Visa 13
            ['340-3161-9380-9364'],  // AmericanExpress
            ['30351042633884'],      // Dinners
        ];
    }

    /**
     * @return array
     */
    public function providerForFail(): array
    {
        return [
            [null],
            [''],
            ['it isnt my credit card number'],
            ['&stR@ng3|) (|-|@r$'],
            ['1234 1234 1234 1234'],
            ['1234.1234.1234.1234'],
        ];
    }
}
