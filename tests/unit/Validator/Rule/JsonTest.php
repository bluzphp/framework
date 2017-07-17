<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */

namespace Bluz\Tests\Validator\Rule;

use Bluz\Tests;
use Bluz\Validator\Rule\Json;

/**
 * Class JsonTest
 *
 * @package Bluz\Tests\Validator\Rule
 */
class JsonTest extends Tests\FrameworkTestCase
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
     *
     * @param $input
     */
    public function testValidJsonsShouldReturnTrue($input)
    {
        self::assertTrue($this->validator->validate($input));
        self::assertTrue($this->validator->assert($input));
    }

    /**
     * @dataProvider providerForFail
     * @expectedException \Bluz\Validator\Exception\ValidatorException
     *
     * @param $input
     */
    public function testInvalidJsonsShouldThrowJsonException($input)
    {
        self::assertFalse($this->validator->validate($input));
        self::assertFalse($this->validator->assert($input));
    }

    /**
     * @return array
     */
    public function providerForPass()
    {
        return array(
            ['2'],
            ['"abc"'],
            ['[1,2,3]'],
            ['["foo", "bar", "number", 1]'],
            ['{"foo": "bar", "number":1}'],
        );
    }

    /**
     * @return array
     */
    public function providerForFail()
    {
        return array(
            [''],
            ['{foo:bar}'],
        );
    }
}
