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
use Bluz\Validator\Rule\Contains;

/**
 * Class ContainsTest
 * @package Bluz\Tests\Validator\Rule
 */
class ContainsTest extends Tests\TestCase
{
    /**
     * @dataProvider providerForPass
     */
    public function testStringsContainingExpectedValueShouldPass($start, $input)
    {
        $v = new Contains($start);
        $this->assertTrue($v->validate($input));
        $this->assertTrue($v->assert($input));
    }

    /**
     * @dataProvider providerForFail
     * @expectedException \Bluz\Validator\Exception\ValidatorException
     */
    public function testStringsNotContainsExpectedValueShouldNotPass($start, $input, $identical = false)
    {
        $v = new Contains($start, $identical);
        $this->assertFalse($v->validate($input));
        $this->assertFalse($v->assert($input));
    }

    /**
     * @return array
     */
    public function providerForPass()
    {
        return array(
            array('foo', array('bar', 'foo')),
            array('foo', 'barbazFOO'),
            array('foo', 'barbazfoo'),
            array('foo', 'foobazfoo'),
            array('1', array(2, 3, 1)),
            array('1', array(2, 3, '1'), true),
        );
    }

    /**
     * @return array
     */
    public function providerForFail()
    {
        return array(
            array('foo', ''),
            array('bat', array('bar', 'foo')),
            array('foo', 'barfaabaz'),
            array('foo', 'barbazFOO', true),
            array('foo', 'faabarbaz'),
        );
    }
}
