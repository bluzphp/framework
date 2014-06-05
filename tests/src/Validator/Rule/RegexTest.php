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
     * Positive
     */
    public function testValidRegexp()
    {
        $v = new Regexp('/^[a-z]+$/');
        $this->assertTrue($v->validate('foobar'));

        $v = new Regexp('/^[a-z]+$/i');
        $this->assertTrue($v->validate('FooBar'));
    }

    /**
     * Negative
     */
    public function testInvalidRegexp()
    {
        $v = new Regexp('/^w+$/');
        $this->assertFalse($v->validate('foo bar'));
    }
}
