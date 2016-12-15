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
use Bluz\Validator\Rule\CountryCode;

/**
 * Class CountryCodeTest
 * @package Bluz\Tests\Validator\Rule
 */
class CountryCodeTest extends Tests\TestCase
{
    /**
     * @var CountryCode
     */
    protected $validator;

    /**
     * Setup validator instance
     */
    protected function setUp()
    {
        parent::setUp();
        $this->validator = new CountryCode;
    }

    /**
     * @dataProvider providerForPass
     * @param $input
     */
    public function testValidCountryCodeShouldReturnTrue($input)
    {
        self::assertTrue($this->validator->validate($input));
    }

    /**
     * @dataProvider providerForFail
     * @param $input
     */
    public function testInvalidCountryCodeShouldReturnFalse($input)
    {
        self::assertFalse($this->validator->validate($input));
    }

    /**
     * @return array
     */
    public function providerForPass()
    {
        return array(
            ['UA'],
        );
    }

    /**
     * @return array
     */
    public function providerForFail()
    {
        return array(
            [''],
            ['UKR'],
        );
    }
}
