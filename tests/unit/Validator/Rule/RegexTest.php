<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Validator\Rule;

use Bluz\Tests;
use Bluz\Validator\Rule\RegexpRule as Rule;

/**
 * Class RegexTest
 *
 * @package Bluz\Tests\Validator\Rule
 */
class RegexTest extends Tests\FrameworkTestCase
{
    /**
     * @dataProvider providerForPass
     *
     * @param $expression
     * @param $input
     */
    public function testValidRegexpShouldPass($expression, $input)
    {
        $rule = new Rule($expression);
        self::assertTrue($rule->validate($input));
        self::assertNotEmpty($rule->__toString());
    }

    /**
     * @dataProvider providerForFail
     *
     * @param $expression
     * @param $input
     */
    public function testInvalidRegexpShouldFail($expression, $input)
    {
        $rule = new Rule($expression);
        self::assertFalse($rule->validate($input));
        self::assertNotEmpty($rule->__toString());
    }

    /**
     * @return array
     */
    public function providerForPass(): array
    {
        return [
            ['/^[a-z]+$/', 'foobar'],
            ['/^[a-z]+$/i', 'FooBar'],
        ];
    }

    /**
     * @return array
     */
    public function providerForFail(): array
    {
        return [
            ['/^[a-z]+$/', 'foo bar'],
            ['/^w+$/', 'foo bar'],
        ];
    }
}
