<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */

namespace Bluz\Tests\Validator\Rule;

use Bluz\Tests;
use Bluz\Validator\Rule\In;

/**
 * Class InTest
 *
 * @package Bluz\Tests\Validator\Rule
 */
class InTest extends Tests\FrameworkTestCase
{
    /**
     * @dataProvider providerForPass
     *
     * @param      $input
     * @param null $haystack
     * @param bool $strict
     */
    public function testSuccessInValidatorCases($input, $haystack = null, $strict = false)
    {
        $v = new In($haystack, $strict);
        self::assertTrue($v->validate($input));
        self::assertTrue($v->assert($input));
    }

    /**
     * @dataProvider providerForFail
     * @expectedException \Bluz\Validator\Exception\ValidatorException
     *
     * @param      $input
     * @param      $haystack
     * @param bool $strict
     */
    public function testInvalidInChecksShouldThrowInException($input, $haystack, $strict = false)
    {
        $v = new In($haystack, $strict);
        self::assertFalse($v->validate($input));
        self::assertNotEmpty($v->__toString());
        self::assertFalse($v->assert($input));
    }

    /**
     * @return array
     */
    public function providerForPass()
    {
        return array(
            ['foo', ['foo', 'bar']],
            ['foo', 'barfoobaz'],
            ['foo', 'foobarbaz'],
            ['foo', 'barbazfoo'],
            ['foo', 'foo', true],
            ['1', [1, 2, 3]],
            ['1', ['1', 2, 3], true],
        );
    }

    /**
     * @return array
     */
    public function providerForFail()
    {
        return array(
            ['', 'barfoobaz'],
            ['', 42],
            ['bat', ['foo', 'bar']],
            ['foo', 'barfaabaz'],
            ['foo', 'faabarbaz'],
            ['foo', 'baabazfaa'],
            ['Foo', 'barbazfoo', true],
            ['1', [1, 2, 3], true],
        );
    }
}
