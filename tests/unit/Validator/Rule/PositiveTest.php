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
use Bluz\Validator\Rule\Positive;

/**
 * Class PositiveTest
 *
 * @package Bluz\Tests\Validator\Rule
 */
class PositiveTest extends Tests\TestCase
{
    /**
     * @var \Bluz\Validator\Rule\Positive
     */
    protected $validator;

    /**
     * Setup validator instance
     */
    protected function setUp()
    {
        parent::setUp();
        $this->validator = new Positive();
    }

    /**
     * @dataProvider providerForPass
     *
     * @param $input
     */
    public function testPositive($input)
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
    public function testNotPositive($input)
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
            [16],
            ['165'],
            [123456],
            [1e10],
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
            ['-1.44'],
            [-1e-5],
            [0],
            [-0],
            [-10],
        );
    }
}
