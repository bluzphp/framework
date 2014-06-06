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
use Bluz\Validator\Rule\Length;

/**
 * Class LengthTest
 * @package Bluz\Tests\Validator\Rule
 */
class LengthTest extends Tests\TestCase
{
    /**
     * @dataProvider providerForPass
     */
    public function testLengthInsideBoundsShouldReturnTrue($string, $min, $max)
    {
        $validator = new Length($min, $max);
        $this->assertTrue($validator->validate($string));
    }

    /**
     * @dataProvider providerForFail
     */
    public function testLengthOutsideValidBoundsShouldThrowLengthException($string, $min, $max)
    {
        $validator = new Length($min, $max);
        $this->assertFalse($validator->validate($string));
    }

    /**
     * @dataProvider providerForFailInclusive
     */
    public function testLengthOutsideBoundsShouldThrowLengthException($string, $min, $max)
    {
        $validator = new Length($min, $max, false);
        $this->assertFalse($validator->validate($string));
        $this->assertNotEmpty($validator->getTemplate());
    }

    /**
     * @dataProvider providerForComponentException
     * @expectedException \Bluz\Validator\Exception\ComponentException
     */
    public function testInvalidConstructorParametersShouldThrowComponentExceptionUponInstantiation($string, $min, $max)
    {
        new Length($min, $max);
    }

    /**
     * @return array
     */
    public function providerForPass()
    {
        return array(
            array('foobar', 1, 15),
            array('ççççç', 4, 6),
            array(range(1, 20), 1, 30),
            array((object) array('foo'=>'bar', 'bar'=>'baz'), 1, 2),
            array('foobar', 1, null), //null is a valid max length, means "no maximum",
            array('foobar', null, 15) //null is a valid min length, means "no minimum"
        );
    }

    /**
     * @return array
     */
    public function providerForFail()
    {
        return array(
            array(0, 1, 3),
            array('foobar', 1, 3),
            array((object) array('foo'=>'bar', 'bar'=>'baz'), 3, 5),
            array(range(1, 50), 1, 30),
        );
    }

    /**
     * @return array
     */
    public function providerForFailInclusive()
    {
        return array(
            array(range(1, 20), 1, 20),
            array('foobar', 1, 6),
            array('foobar', 6, null), // null is a valid max length, means "no maximum",
            array('foobar', null, 6)  // null is a valid min length, means "no minimum"
        );
    }

    /**
     * @return array
     */
    public function providerForComponentException()
    {
        return array(
            array('foobar', 'a', 15),
            array('foobar', 1, 'abc d'),
            array('foobar', 10, 1),
        );
    }
}
