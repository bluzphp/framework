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
use Bluz\Validator\Rule\Integer;

class IntegerTest extends Tests\TestCase
{
    /**
     * @var Integer
     */
    protected $validator;

    protected function setUp()
    {
        $this->validator = new Integer;
    }

    /**
     * @dataProvider providerForInt
     *
     */
    public function testValidIntegersShouldReturnTrue($input)
    {
        $this->assertTrue($this->validator->validate($input));
    }

    /**
     * @dataProvider providerForNotInt
     */
    public function testInvalidIntegersShouldReturnFalse($input)
    {
        $this->assertFalse($this->validator->validate($input));
    }

    /**
     * @return array
     */
    public function providerForInt()
    {
        return array(
            array(16),
            array('165'),
            array(123456),
            array(PHP_INT_MAX),
        );
    }

    /**
     * @return array
     */
    public function providerForNotInt()
    {
        return array(
            array(null),
            array(''),
            array(' '),
            array('a'),
            array('Foo'),
            array('1.44'),
            array(1e-5),
        );
    }
}
