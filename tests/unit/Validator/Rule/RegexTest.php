<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Validator\Rule;

use Bluz\Tests;
use Bluz\Validator\Rule\Regexp;

/**
 * Class RegexTest
 * @package Bluz\Tests\Validator\Rule
 */
class RegexTest extends Tests\TestCase
{
    /**
     * @dataProvider providerForPass
     * @param $expression
     * @param $input
     */
    public function testValidRegexp($expression, $input)
    {
        $v = new Regexp($expression);
        self::assertTrue($v->validate($input));
    }

    /**
     * @dataProvider providerForFail
     * @param $expression
     * @param $input
     */
    public function testInvalidRegexp($expression, $input)
    {
        $v = new Regexp($expression);
        self::assertFalse($v->validate($input));
    }

    /**
     * @return array
     */
    public function providerForPass()
    {
        return array(
            ['/^[a-z]+$/', 'foobar'],
            ['/^[a-z]+$/i', 'FooBar'],
        );
    }

    /**
     * @return array
     */
    public function providerForFail()
    {
        return array(
            ['/^[a-z]+$/', 'foo bar'],
            ['/^w+$/', 'foo bar'],
        );
    }
}
