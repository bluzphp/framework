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
use Bluz\Validator\Rule\Equals;

/**
 * Class EqualsTest
 * @package Bluz\Tests\Validator\Rule
 */
class EqualsTest extends Tests\TestCase
{
    /**
     * @dataProvider providerForPass
     */
    public function testStringsContainingExpectedValueShouldPass($start, $input)
    {
        $validator = new Equals($start);
        $this->assertTrue($validator->validate($input));
        $this->assertTrue($validator->assert($input));
    }

    /**
     * @dataProvider providerForFail
     * @expectedException \Bluz\Validator\Exception\ValidatorException
     */
    public function testStringsNotEqualsExpectedValueShouldNotPass($start, $input, $identical = false)
    {
        $validator = new Equals($start, $identical);
        $this->assertFalse($validator->validate($input));
        $this->assertNotEmpty($validator->__toString());
        $this->assertFalse($validator->assert($input));
    }

    /**
     * @return array
     */
    public function providerForPass()
    {
        return array(
            array('foo', 'foo'),
            array(10, "10"),
        );
    }

    /**
     * @return array
     */
    public function providerForFail()
    {
        return array(
            array('foo', ''),
            array('foo', 'bar'),
            array(10, "10", true),
        );
    }
}
