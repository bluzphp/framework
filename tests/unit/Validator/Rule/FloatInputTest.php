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
use Bluz\Validator\Rule\FloatInput;

/**
 * Class FloatTest
 *
 * @package Bluz\Tests\Validator\Rule
 */
class FloatInputTest extends Tests\FrameworkTestCase
{
    /**
     * @var \Bluz\Validator\Rule\FloatInput
     */
    protected $validator;

    /**
     * Setup validator instance
     */
    protected function setUp()
    {
        parent::setUp();
        $this->validator = new FloatInput();
    }

    /**
     * @dataProvider providerForPass
     *
     * @param $input
     */
    public function testFloatNumbersShouldPass($input)
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
    public function testNotFloatNumbersShouldFail($input)
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
            [165],
            [1],
            [0],
            [0.0],
            ['1'],
            ['19347e12'],
            [165.0],
            ['165.7'],
            [1e12],
        );
    }

    /**
     * @return array
     */
    public function providerForFail()
    {
        return array(
            [null],
            [''],
            ['a'],
            [' '],
            ['Foo'],
        );
    }
}
