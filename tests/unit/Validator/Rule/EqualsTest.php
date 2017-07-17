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
use Bluz\Validator\Rule\Equals;

/**
 * Class EqualsTest
 *
 * @package Bluz\Tests\Validator\Rule
 */
class EqualsTest extends Tests\FrameworkTestCase
{
    /**
     * @dataProvider providerForPass
     *
     * @param $start
     * @param $input
     */
    public function testStringsContainingExpectedValueShouldPass($start, $input)
    {
        $validator = new Equals($start);
        self::assertTrue($validator->validate($input));
        self::assertTrue($validator->assert($input));
    }

    /**
     * @dataProvider providerForFail
     * @expectedException \Bluz\Validator\Exception\ValidatorException
     *
     * @param      $start
     * @param      $input
     * @param bool $identical
     */
    public function testStringsNotEqualsExpectedValueShouldNotPass($start, $input, $identical = false)
    {
        $validator = new Equals($start, $identical);
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
            ['foo', 'foo'],
            [10, "10"],
        );
    }

    /**
     * @return array
     */
    public function providerForFail()
    {
        return array(
            ['foo', ''],
            ['foo', 'bar'],
            [10, "10", true],
        );
    }
}
