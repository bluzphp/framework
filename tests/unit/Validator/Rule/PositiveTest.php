<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Validator\Rule;

use Bluz\Tests;
use Bluz\Validator\Rule\PositiveRule as Rule;

/**
 * Class PositiveTest
 *
 * @package Bluz\Tests\Validator\Rule
 */
class PositiveTest extends Tests\FrameworkTestCase
{
    /**
     * @var Rule
     */
    protected $rule;

    /**
     * Setup validator instance
     */
    protected function setUp(): void
    {
        $this->rule = new Rule();
    }

    /**
     * @dataProvider providerForPass
     *
     * @param $input
     */
    public function testPositiveNumbersShouldPass($input)
    {
        self::assertTrue($this->rule->validate($input));
        self::assertNotEmpty($this->rule->__toString());
    }

    /**
     * @dataProvider providerForFail
     *
     * @param $input
     */
    public function testNegativeNumbersShouldFail($input)
    {
        self::assertFalse($this->rule->validate($input));
        self::assertNotEmpty($this->rule->__toString());
    }

    /**
     * @return array
     */
    public function providerForPass(): array
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
    public function providerForFail(): array
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
