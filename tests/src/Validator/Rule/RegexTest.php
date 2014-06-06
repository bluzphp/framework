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
     */
    public function testValidRegexp($expression, $input)
    {
        $v = new Regexp($expression);
        $this->assertTrue($v->validate($input));
    }

    /**
     * @dataProvider providerForFail
     */
    public function testInvalidRegexp($expression, $input)
    {
        $v = new Regexp($expression);
        $this->assertFalse($v->validate($input));
    }

    /**
     * @return array
     */
    public function providerForPass()
    {
        return array(
            array('/^[a-z]+$/', 'foobar'),
            array('/^[a-z]+$/i', 'FooBar'),
        );
    }

    /**
     * @return array
     */
    public function providerForFail()
    {
        return array(
            array('/^[a-z]+$/', 'foo bar'),
            array('/^w+$/', 'foo bar'),
        );
    }
}
