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
     * @param $input
     */
    public function testValidVersionShouldReturnTrue($input)
    {
        $rule = new Version();
        self::assertTrue($rule->validate($input));
        self::assertTrue($rule->assert($input));
    }

    /**
     * @dataProvider providerForFail
     * @expectedException \Bluz\Validator\Exception\ValidatorException
     * @param $input
     */
    public function testInvalidVersionShouldThrowException($input)
    {
        $rule = new Version();
        self::assertFalse($rule->validate($input));
        self::assertFalse($rule->assert($input));
    }

    /**
     * @return array
     */
    public function providerForPass()
    {
        return array(
            ['1.0.0'],
            ['1.0.0-alpha'],
            ['1.0.0-alpha.1'],
            ['1.0.0-0.3.7'],
            ['1.0.0-x.7.z.92'],
            ['1.3.7+build.2.b8f12d7'],
            ['1.3.7-rc.1'],
        );
    }

    /**
     * @return array
     */
    public function providerForFail()
    {
        return array(
            [''],
            ['1.3.7--'],
            ['1.3.7++'],
            ['foo'],
            ['1.2.3.4'],
            ['1.2.3.4-beta'],
            ['beta'],
        );
    }
}
