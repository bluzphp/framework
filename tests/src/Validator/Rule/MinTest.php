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
use Bluz\Validator\Rule\Min;

/**
 * Class MinTest
 * @package Bluz\Tests\Validator\Rule
 */
class MinTest extends Tests\TestCase
{
    /**
     * @dataProvider providerForPass
     */
    public function testValidMinShouldReturnTrue($minValue, $inclusive, $input)
    {
        $min = new Min($minValue, $inclusive);
        $this->assertTrue($min->validate($input));
        $this->assertNotEmpty($min->__toString());
    }

    /**
     * @dataProvider providerForFail
     */
    public function testInvalidMinShouldReturnFalse($minValue, $inclusive, $input)
    {
        $min = new Min($minValue, $inclusive);
        $this->assertFalse($min->validate($input));
    }

    /**
     * @return array
     */
    public function providerForPass()
    {
        return array(
            array(0, true, ''),       // empty string is equal zero
            array(100, false, 123.0),
            array(100, true, 100),
            array(-50, true, -50),
            array(-50, false, 100),
            array(100, false, 200),
        );
    }

    /**
     * @return array
     */
    public function providerForFail()
    {
        return array(
            array(0, false, ''),     // empty string is equal zero
            array(100, false, ''),
            array(100, false, 50),
            array(0, false, -50),
            array(50, false, 50),
        );
    }
}
