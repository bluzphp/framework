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
use Bluz\Validator\Rule\ArrayInput;

/**
 * Class StringTest
 * @package Bluz\Tests\Validator\Rule
 */
class ArrayInputTest extends Tests\TestCase
{
    /**
     * @var \Bluz\Validator\Rule\String
     */
    protected $validator;

    protected function setUp()
    {
        $this->validator = new ArrayInput('is_numeric');
    }

    /**
     * @dataProvider providerForPass
     */
    public function testArray($input)
    {
        $this->assertTrue($this->validator->validate($input));
    }

    /**
     * @dataProvider providerForFail
     */
    public function testInvalidArray($input)
    {
        $this->assertFalse($this->validator->validate($input));
    }

    /**
     * @return array
     */
    public function providerForPass()
    {
        return array(
            array(array()),
            array([1, 2, 3]),
            array(['1', '2', '3']),
            array(['1.2', '2e10']),
        );
    }

    /**
     * @return array
     */
    public function providerForFail()
    {
        return array(
            array(null),
            array(150),
            array('abc'),
            array(['abc']),
            array(['abc', 1, 2, 3]),
        );
    }
}
