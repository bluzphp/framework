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
use Bluz\Validator\Rule\In;

/**
 * Class InTest
 * @package Bluz\Tests\Validator\Rule
 */
class InTest extends Tests\TestCase
{
    /**
     * @dataProvider providerForPass
     */
    public function testSuccessInValidatorCases($input, $options = null)
    {
        $v = new In($options);
        $this->assertTrue($v->validate($input));
        $this->assertTrue($v->assert($input));
    }

    /**
     * @dataProvider providerForFail
     * @expectedException \Bluz\Validator\Exception\ValidatorException
     */
    public function testInvalidInChecksShouldThrowInException($input, $options, $strict = false)
    {
        $v = new In($options, $strict);
        $this->assertFalse($v->validate($input));
        $this->assertNotEmpty($v->__toString($input));
        $this->assertFalse($v->assert($input));
    }

    /**
     * @return array
     */
    public function providerForPass()
    {
        return array(
            array('foo', array('foo', 'bar')),
            array('foo', 'barfoobaz'),
            array('foo', 'foobarbaz'),
            array('foo', 'barbazfoo'),
            array('foo', 'barbazfoo', true),
            array('1', array(1, 2, 3)),
            array('1', array('1', 2, 3), true),
        );
    }

    /**
     * @return array
     */
    public function providerForFail()
    {
        return array(
            array('', 'barfoobaz'),
            array('', 42),
            array('bat', array('foo', 'bar')),
            array('foo', 'barfaabaz'),
            array('foo', 'faabarbaz'),
            array('foo', 'baabazfaa'),
            array('1', array(1, 2, 3), true),
        );
    }
}
