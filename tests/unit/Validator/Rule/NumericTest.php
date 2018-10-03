<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Validator\Rule;

use Bluz\Tests;
use Bluz\Validator\Rule\NumericRule as Rule;

/**
 * Class NumericTest
 *
 * @package Bluz\Tests\Validator\Rule
 */
class NumericTest extends Tests\FrameworkTestCase
{
    /**
     * @var Rule
     */
    protected $rule;

    /**
     * Setup validator instance
     */
    protected function setUp()
    {
        $this->rule = new Rule;
    }

    /**
     * @dataProvider providerForPass
     *
     * @param $input
     */
    public function testValidNumericShouldPass($input)
    {
        self::assertTrue($this->rule->validate($input));
        self::assertNotEmpty($this->rule->__toString());
    }

    /**
     * @dataProvider providerForFail
     *
     * @param $input
     */
    public function testNotNumericShouldFail($input)
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
    public function providerForFail(): array
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
