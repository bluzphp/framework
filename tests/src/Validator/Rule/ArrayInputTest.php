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
     * @var \Bluz\Validator\Rule\StringInput
     */
    protected $validator;

    protected function setUp()
    {
        $this->validator = new ArrayInput('is_numeric');
    }

    /**
     * @dataProvider providerForPass
     * @param $input
     */
    public function testArray($input)
    {
        self::assertTrue($this->validator->validate($input));
    }

    /**
     * @dataProvider providerForFail
     * @param $input
     */
    public function testInvalidArray($input)
    {
        self::assertFalse($this->validator->validate($input));
    }

    /**
     * @return array
     */
    public function providerForPass()
    {
        return array(
            [[]],
            [[1, 2, 3]],
            [['1', '2', '3']],
            [['1.2', '2e10']],
        );
    }

    /**
     * @return array
     */
    public function providerForFail()
    {
        return array(
            [null],
            [150],
            ['abc'],
            [['abc']],
            [['abc', 1, 2, 3]],
        );
    }
}
