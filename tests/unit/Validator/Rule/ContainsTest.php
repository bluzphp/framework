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
     * @param $start
     * @param $input
     */
    public function testStringsContainingExpectedValueShouldPass($start, $input)
    {
        $validator = new Contains($start);
        self::assertTrue($validator->validate($input));
        self::assertTrue($validator->assert($input));
    }

    /**
     * @dataProvider providerForFail
     * @expectedException \Bluz\Validator\Exception\ValidatorException
     * @param $start
     * @param $input
     * @param bool $identical
     */
    public function testStringsNotContainsExpectedValueShouldNotPass($start, $input, $identical = false)
    {
        $validator = new Contains($start, $identical);
        self::assertFalse($validator->validate($input));
        self::assertNotEmpty($validator->__toString());
        self::assertFalse($validator->assert($input));
    }

    /**
     * @return array
     */
    public function providerForPass()
    {
        return array(
            ['foo', ['bar', 'foo']],
            ['foo', 'barbazFOO'],
            ['foo', 'barbazfoo'],
            ['foo', 'foobazfoo'],
            ['1', [2, 3, 1]],
            ['1', [2, 3, '1'], true],
        );
    }

    /**
     * @return array
     */
    public function providerForFail()
    {
        return array(
            ['foo', ''],
            ['bat', ['bar', 'foo']],
            ['foo', 'barfaabaz'],
            ['foo', 'barbazFOO', true],
            ['foo', 'faabarbaz'],
        );
    }
}
