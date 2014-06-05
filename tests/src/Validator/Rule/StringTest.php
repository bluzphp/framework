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
use Bluz\Validator\Rule\String;

/**
 * Class StringTest
 * @package Bluz\Tests\Validator\Rule
 */
class StringTest extends Tests\TestCase
{
    /**
     * @var String
     */
    protected $validator;

    protected function setUp()
    {
        $this->validator = new String;
    }

    /**
     * @dataProvider providerForString
     *
     */
    public function testString($input)
    {
        $this->assertTrue($this->validator->validate($input));
    }

    /**
     * @dataProvider providerForNotString
     */
    public function testNotString($input)
    {
        $this->assertFalse($this->validator->validate($input));
    }

    /**
     * @return array
     */
    public function providerForString()
    {
        return array(
            array(''),
            array('165.7'),
        );
    }

    /**
     * @return array
     */
    public function providerForNotString()
    {
        return array(
            array(null),
            array(array()),
            array(new \stdClass),
            array(150)
        );
    }
}
