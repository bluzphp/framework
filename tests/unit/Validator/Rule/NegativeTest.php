<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Validator\Rule;

use Bluz\Tests;
use Bluz\Validator\Rule\NegativeRule as Rule;

/**
 * Class NegativeTest
 *
 * @package Bluz\Tests\Validator\Rule
 */
class NegativeTest extends Tests\FrameworkTestCase
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
    public function testNegativeShouldPass($input)
    {
        self::assertTrue($this->rule->validate($input));
        self::assertNotEmpty($this->rule->__toString());
    }

    /**
     * @dataProvider providerForFail
     *
     * @param $input
     */
    public function testNegativeShouldFail($input)
    {
        self::assertFalse($this->rule->validate($input));
        self::assertNotEmpty($this->rule->__toString());
    }

    /**
     * @return array
     */
    public function providerForPass(): array
    {
        return [
            ['-1.44'],
            [-1e-5],
            [-10],
        ];
    }

    /**
     * @return array
     */
    public function providerForFail(): array
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
