<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Validator\Rule;

use Bluz\Tests;
use Bluz\Validator\Rule\ArrayRule as Rule;

/**
 * Class StringTest
 *
 * @package Bluz\Tests\Validator\Rule
 */
class ArrayInputTest extends Tests\FrameworkTestCase
{
    /**
     * @var Rule
     */
    protected $rule;

    protected function setUp()
    {
        $this->rule = new Rule('is_numeric');
    }

    /**
     * @dataProvider providerForPass
     *
     * @param $input
     */
    public function testArrayWithNumbersShouldPass($input)
    {
        self::assertTrue($this->rule->validate($input));
        self::assertNotEmpty($this->rule->__toString());
    }

    /**
     * @dataProvider providerForFail
     *
     * @param $input
     */
    public function testArrayMixedArrayShouldFail($input)
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
            [[]],
            [[1, 2, 3]],
            [['1', '2', '3']],
            [['1.2', '2e10']],
        ];
    }

    /**
     * @return array
     */
    public function providerForFail(): array
    {
        return [
            [null],
            [150],
            ['abc'],
            [['abc']],
            [['abc', 1, 2, 3]],
        ];
    }
}
