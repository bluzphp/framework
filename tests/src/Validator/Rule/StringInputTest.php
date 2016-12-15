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
use Bluz\Validator\Rule\StringInput;

/**
 * Class StringTest
 * @package Bluz\Tests\Validator\Rule
 */
class StringInputTest extends Tests\TestCase
{
    /**
     * @var \Bluz\Validator\Rule\StringInput
     */
    protected $validator;

    protected function setUp()
    {
        $this->validator = new StringInput;
    }

    /**
     * @dataProvider providerForPass
     * @param $input
     */
    public function testString($input)
    {
        self::assertTrue($this->validator->validate($input));
    }

    /**
     * @dataProvider providerForFail
     * @param $input
     */
    public function testNotString($input)
    {
        self::assertFalse($this->validator->validate($input));
    }

    /**
     * @return array
     */
    public function providerForPass()
    {
        return array(
            [''],
            ['165.7'],
        );
    }

    /**
     * @return array
     */
    public function providerForFail()
    {
        return array(
            [null],
            [[]],
            [new \stdClass],
            [150]
        );
    }
}
