<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Unit\Validator\Rule;

use Bluz\Tests\Unit\Unit;
use Bluz\Validator\Rule\JsonRule as Rule;

/**
 * Class JsonTest
 *
 * @package Bluz\Tests\Validator\Rule
 */
class JsonTest extends Unit
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
    public function testValidJsonsShouldPass($input)
    {
        self::assertTrue($this->rule->validate($input));
        self::assertNotEmpty($this->rule->__toString());
    }

    /**
     * @dataProvider providerForFail
     *
     * @param $input
     */
    public function testInvalidJsonsShouldFail($input)
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
            ['2'],
            ['"abc"'],
            ['[1,2,3]'],
            ['["foo", "bar", "number", 1]'],
            ['{"foo": "bar", "number":1}'],
        );
    }

    /**
     * @return array
     */
    public function providerForFail(): array
    {
        return [
            [''],
            ['{foo:bar}'],
        ];
    }
}
