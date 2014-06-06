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
use Bluz\Validator\Rule\Json;

/**
 * Class JsonTest
 * @package Bluz\Tests\Validator\Rule
 */
class JsonTest extends Tests\TestCase
{
    /**
     * @var \Bluz\Validator\Rule\Json
     */
    protected $validator;

    /**
     * Setup validator instance
     */
    protected function setUp()
    {
        parent::setUp();
        $this->validator = new Json();
    }
    /**
     * @dataProvider providerForPass
     */
    public function testValidJsonsShouldReturnTrue($input)
    {
        $this->assertTrue($this->validator->validate($input));
        $this->assertTrue($this->validator->assert($input));
    }

    /**
     * @dataProvider providerForFail
     * @expectedException \Bluz\Validator\Exception\ValidatorException
     */
    public function testInvalidJsonsShouldThrowJsonException($input)
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
            array('2'),
            array('"abc"'),
            array('[1,2,3]'),
            array('["foo", "bar", "number", 1]'),
            array('{"foo": "bar", "number":1}'),
        );
    }

    /**
     * @return array
     */
    public function providerForFail()
    {
        return array(
            array(''),
            array('{foo:bar}'),
        );
    }
}
