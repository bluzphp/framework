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
use Bluz\Validator\Rule\Condition;

/**
 * Class AlphaTest
 * @package Bluz\Tests\Validator\Rule
 */
class ConditionTest extends Tests\TestCase
{
    /**
     * @dataProvider providerForPass
     */
    public function testValidAlphanumericCharsShouldReturnTrue($condition, $input = 'any')
    {
        $validator = new Condition($condition);
        $this->assertTrue($validator->validate($input));
        $this->assertTrue($validator->assert($input));
    }

    /**
     * @dataProvider providerForFail
     * @expectedException \Bluz\Validator\Exception\ValidatorException
     */
    public function testInvalidAlphanumericCharsShouldReturnFalse($condition, $input = 'any')
    {

        $validator = new Condition($condition);
        $this->assertFalse($validator->validate($input));
        $this->assertFalse($validator->assert($input));
    }

    /**
     * @return array
     */
    public function providerForPass()
    {
        return array(
            array((4 > 2), 'always'),
            array((is_int(42)), 'always'),
        );
    }

    /**
     * @return array
     */
    public function providerForFail()
    {
        return array(
            array((4 < 2), 'always'),
            array((is_int(42.0204)), 'always'),
        );
    }
}
