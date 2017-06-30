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
use Bluz\Validator\Rule\Negative;

/**
 * Class NegativeTest
 *
 * @package Bluz\Tests\Validator\Rule
 */
class NegativeTest extends Tests\TestCase
{
    /**
     * @var \Bluz\Validator\Rule\Negative
     */
    protected $validator;

    /**
     * Setup validator instance
     */
    protected function setUp()
    {
        parent::setUp();
        $this->validator = new Negative();
    }

    /**
     * @dataProvider providerForPass
     *
     * @param $input
     */
    public function testNegativeShouldPass($input)
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
    public function testNotNegativeNumbersShouldThrowNegativeException($input)
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
            ['-1.44'],
            [-1e-5],
            [-10],
        );
    }

    /**
     * @return array
     */
    public function providerForFail()
    {
        return array(
            [0],
            [-0],
            [null],
            [''],
            ['a'],
            [' '],
            ['Foo'],
            [16],
            ['165'],
            [123456],
            [1e10],
        );
    }
}
