<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Unit\Validator\Rule;

use Bluz\Tests\Unit\Unit;
use Bluz\Validator\Rule\StringRule as Rule;

/**
 * Class StringTest
 *
 * @package Bluz\Tests\Validator\Rule
 */
class StringInputTest extends Unit
{
    /**
     * @var Rule
     */
    protected $rule;

    protected function setUp(): void
    {
        $this->rule = new Rule();
    }

    /**
     * @dataProvider providerForPass
     *
     * @param $input
     */
    public function testValidStringShouldPass($input)
    {
        self::assertTrue($this->rule->validate($input));
        self::assertNotEmpty($this->rule->__toString());
    }

    /**
     * @dataProvider providerForFail
     *
     * @param $input
     */
    public function testInvalidStringShouldFail($input)
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
            [''],
            ['165.7'],
        ];
    }

    /**
     * @return array
     */
    public function providerForFail(): array
    {
        return [
            [null],
            [[]],
            [new \stdClass()],
            [150]
        ];
    }
}
