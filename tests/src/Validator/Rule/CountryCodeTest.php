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
     */
    public function testValidCountryCodeShouldReturnTrue($input)
    {
        $this->assertTrue($this->validator->validate($input));
    }

    /**
     * @dataProvider providerForFail
     */
    public function testInvalidCountryCodeShouldReturnFalse($input)
    {
        $this->assertFalse($this->validator->validate($input));
    }

    /**
     * @return array
     */
    public function providerForPass()
    {
        return array(
            array('UA'),
        );
    }

    /**
     * @return array
     */
    public function providerForFail()
    {
        return array(
            array(''),
            array('UKR'),
        );
    }
}
