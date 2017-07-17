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
use Bluz\Validator\Rule\Numeric;

/**
 * Class NumericTest
 *
 * @package Bluz\Tests\Validator\Rule
 */
class NumericTest extends Tests\FrameworkTestCase
{
    /**
     * @var Numeric
     */
    protected $validator;

    /**
     * Setup validator instance
     */
    protected function setUp()
    {
        parent::setUp();
        $this->validator = new Numeric;
    }

    /**
     * @dataProvider providerForPass
     *
     * @param $input
     */
    public function testNumeric($input)
    {
        self::assertTrue($this->validator->validate($input));
    }

    /**
     * @dataProvider providerForFail
     *
     * @param $input
     */
    public function testNotNumeric($input)
    {
        self::assertFalse($this->validator->validate($input));
    }

    /**
     * @return array
     */
    public function providerForPass()
    {
        return array(
            [165],
            [165.0],
            [-165],
            ['165'],
            ['165.0'],
            ['+165.0'],
        );
    }

    /**
     * @return array
     */
    public function providerForFail()
    {
        return array(
            [null],
            ['a'],
            [''],
            [' '],
            ['Foo'],
        );
    }
}
