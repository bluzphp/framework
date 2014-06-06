<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Validator\Rule;

use \DateTime;
use Bluz\Tests;
use Bluz\Validator\Rule\Between;

/**
 * Class BetweenTest
 * @package Bluz\Tests\Validator\Rule
 */
class BetweenTest extends Tests\TestCase
{
    /**
     * @dataProvider providerForPass
     */
    public function testValuesBetweenBoundsShouldPass($min, $max, $inclusive, $input)
    {
        $validator = new Between($min, $max, $inclusive);
        $this->assertTrue($validator->validate($input));
        $this->assertTrue($validator->assert($input));
    }

    /**
     * @dataProvider providerForFail
     * @expectedException \Bluz\Validator\Exception\ValidatorException
     */
    public function testValuesOutBoundsShouldRaiseException($min, $max, $inclusive, $input)
    {
        $validator = new Between($min, $max, $inclusive);
        $this->assertFalse($validator->validate($input));
        $this->assertNotEmpty($validator->__toString());
        $this->assertFalse($validator->assert($input));
    }

    /**
     * @dataProvider providerForComponentException
     * @expectedException \Bluz\Validator\Exception\ComponentException
     */
    public function testInvalidConstructionParamsShouldRaiseException($min, $max)
    {
        new Between($min, $max);
    }

    /**
     * @return array
     */
    public function providerForPass()
    {
        return array(
            array(0, 1, true, 0),
            array(0, 1, true, 1),
            array(10, 20, false, 15),
            array(10, 20, true, 20),
            array(-10, 20, false, -5),
            array(-10, 20, false, 0),
            array('a', 'z', false, 'j'),
            array(
                new DateTime('yesterday'),
                new DateTime('tomorrow'),
                false,
                new DateTime('now')
            ),
        );
    }

    /**
     * @return array
     */
    public function providerForFail()
    {
        return array(
            array(10, 20, true, ''),
            array(10, 20, false, ''),
            array(0, 1, false, 0),
            array(0, 1, false, 1),
            array(0, 1, false, 2),
            array(0, 1, false, -1),
            array(10, 20, false, 999),
            array(10, 20, false, 20),
            array(-10, 20, false, -11),
            array('a', 'j', false, 'z'),
            array(
                new DateTime('yesterday'),
                new DateTime('now'),
                false,
                new DateTime('tomorrow')
            ),
        );
    }

    /**
     * @return array
     */
    public function providerForComponentException()
    {
        return array(
            array(10, 5),
            array(10, null),
            array(null, 5),
        );
    }
}
