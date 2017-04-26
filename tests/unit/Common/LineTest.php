<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Common;

use Bluz\Common\Line;
use Bluz\Tests\TestCase;

/**
 * Tests for Line helpers
 *
 * @package  Bluz\Tests\Common
 *
 * @author   Anton Shevchuk
 * @created  21.04.17 12:36
 */
class LineTest extends TestCase
{

    /**
     * @dataProvider dataForStrTrimEnd
     * @param string $input
     * @param string $output
     */
    public function testToCamelCase($input, $output)
    {
        self::assertEquals($output, to_camel_case($input));
        self::assertEquals($output, Line::toCamelCase($input));
    }

    public function dataForStrTrimEnd() : array
    {
        return [
            ['foo bar', 'FooBar'],
            ['some-unique-id', 'SomeUniqueId'],
            ['another_unique_id', 'AnotherUniqueId'],
            ['Another mixed_unique-ID', 'AnotherMixedUniqueId'],
        ];
    }
}
