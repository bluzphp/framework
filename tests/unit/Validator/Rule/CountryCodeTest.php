<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Validator\Rule;

use Bluz\Tests;
use Bluz\Validator\Rule\CountryCodeRule as Rule;

/**
 * Class CountryCodeTest
 *
 * @package Bluz\Tests\Validator\Rule
 */
class CountryCodeTest extends Tests\FrameworkTestCase
{
    /**
     * @var Rule
     */
    protected $rule;

    /**
     * Setup validator instance
     */
    protected function setUp()
    {
        $this->rule = new Rule;
    }

    /**
     * @dataProvider providerForPass
     *
     * @param $input
     */
    public function testValidCountryCodeShouldPass($input)
    {
        self::assertTrue($this->rule->validate($input));
        self::assertNotEmpty($this->rule->__toString());
    }

    /**
     * @dataProvider providerForFail
     *
     * @param $input
     */
    public function testInvalidCountryCodeShouldFail($input)
    {
        self::assertFalse($this->rule->validate($input));
        self::assertNotEmpty($this->rule->__toString());
    }

    /**
     * @return array
     */
    public function providerForPass() : array
    {
        return [
            ['UA'],
        ];
    }

    /**
     * @return array
     */
    public function providerForFail() : array
    {
        return [
            [''],
            ['UKR'],
        ];
    }
}
