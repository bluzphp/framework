<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */

namespace Bluz\Tests;

use Bluz\Tests\FrameworkTestCase;

/**
 * FunctionsTest
 *
 * @package  Bluz\Tests
 * @author   Anton Shevchuk
 */
class FunctionsTest extends FrameworkTestCase
{
    /**
     * @dataProvider dataForStrTrimEnd
     */
    public function testStrTrimEndFunction($input, $symbol, $output)
    {
        self::assertEquals($output, str_trim_end($input, $symbol));
    }

    public function dataForStrTrimEnd(): array
    {
        return [
            ['foo/bar', '/', 'foo/bar/'],
            ['foo/bar', '/bar/', 'foo/bar/'],
            ['foobar/bar', '/bar/', 'foo/bar/'],
            ['faabar/bar', '/bar/', 'f/bar/'],
            ['foo/bar/', '/1', 'foo/bar/1'],
            ['foo/bar/1/1/1/1/', '/1', 'foo/bar/1'],
        ];
    }
}
