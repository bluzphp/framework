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
use Bluz\Validator\Rule\Version;

/**
 * Class VersionTest
 * @package Bluz\Tests\Validator\Rule
 */
class VersionTest extends Tests\TestCase
{
    /**
     * @dataProvider providerForPass
     */
    public function testValidVersionShouldReturnTrue($input)
    {
        $rule = new Version();
        $this->assertTrue($rule->validate($input));
        $this->assertTrue($rule->assert($input));
    }

    /**
     * @dataProvider providerForFail
     * @expectedException \Bluz\Validator\Exception\ValidatorException
     */
    public function testInvalidVersionShouldThrowException($input)
    {
        $rule = new Version();
        $this->assertFalse($rule->validate($input));
        $this->assertFalse($rule->assert($input));
    }

    /**
     * @return array
     */
    public function providerForPass()
    {
        return array(
            array('1.0.0'),
            array('1.0.0-alpha'),
            array('1.0.0-alpha.1'),
            array('1.0.0-0.3.7'),
            array('1.0.0-x.7.z.92'),
            array('1.3.7+build.2.b8f12d7'),
            array('1.3.7-rc.1'),
        );
    }

    /**
     * @return array
     */
    public function providerForFail()
    {
        return array(
            array(''),
            array('1.3.7--'),
            array('1.3.7++'),
            array('foo'),
            array('1.2.3.4'),
            array('1.2.3.4-beta'),
            array('beta'),
        );
    }
}
