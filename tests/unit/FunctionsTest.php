<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */

namespace Bluz\Tests;

/**
 * FunctionsTest
 *
 * @package  Bluz\Tests
 * @author   Anton Shevchuk
 */
class FunctionsTest extends FrameworkTestCase
{
    /**
     * @dataProvider dataForClassNamespace
     */
    public function testClassNamespaceFunction($input, $output)
    {
        self::assertEquals($output, class_namespace($input));
    }

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

    public function dataForClassNamespace(): array
    {
        return [
            ['Application\\Library\\Some\\Class\\Interface', 'Application\\Library\\Some\\Class'],
            ['Application\\Pages\\Crud', 'Application\\Pages'],
            ['Application\\Pages\\Row', 'Application\\Pages'],
            ['Application\\Pages\\Table', 'Application\\Pages'],
        ];
    }
}
