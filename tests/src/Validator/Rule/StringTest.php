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
use Bluz\Validator\Rule\StringRule as StringValidator;

/**
 * Class StringTest
 * @package Bluz\Tests\Validator\Rule
 */
class StringTest extends Tests\TestCase
{
    /**
     * @var \Bluz\Validator\Rule\StringRule
     */
    protected $validator;

    protected function setUp()
    {
        $this->validator = new StringValidator;
    }

    /**
     * @dataProvider providerForPass
     */
    public function testString($input)
    {
        $this->assertTrue($this->validator->validate($input));
    }

    /**
     * @dataProvider providerForFail
     */
    public function testNotString($input)
    {
        $this->assertFalse($this->validator->validate($input));
    }

    /**
     * @return array
     */
    public function providerForPass()
    {
        return array(
            array(''),
            array('165.7'),
        );
    }

    /**
     * @return array
     */
    public function providerForFail()
    {
        return array(
            array(null),
            array(array()),
            array(new \stdClass),
            array(150)
        );
    }
}
