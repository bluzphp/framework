<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Validator\Rule;

use Bluz\Tests;
use Bluz\Validator\Exception\ComponentException;
use Bluz\Validator\Rule\IpRule as Rule;

/**
 * Class IpTest
 *
 * @package Bluz\Tests\Validator\Rule
 */
class IpTest extends Tests\FrameworkTestCase
{
    /**
     * @dataProvider providerForPass
     *
     * @param      $input
     * @param null $options
     */
    public function testValidIpsShouldPass($input, $options = null)
    {
        $rule = new Rule($options);
        self::assertTrue($rule->validate($input));
        self::assertNotEmpty($rule->__toString());
    }

    /**
     * @dataProvider providerForFail
     *
     * @param      $input
     * @param null $options
     */
    public function testInvalidIpsShouldFail($input, $options = null)
    {
        $rule = new Rule($options);
        self::assertFalse($rule->validate($input));
        self::assertNotEmpty($rule->__toString());
    }

    /**
     * @dataProvider providerForComponentException
     *
     * @param $range
     */
    public function testInvalidRangeShouldRaiseException($range)
    {
        $this->expectException(ComponentException::class);
        new Rule($range);
    }

    /**
     * @dataProvider providerForIpBetweenRange
     *
     * @param $input
     * @param $networkRange
     */
    public function testIpsBetweenRangeShouldPass($input, $networkRange)
    {
        $rule = new Rule($networkRange);
        self::assertTrue($rule->validate($input));
        self::assertNotEmpty($rule->__toString());
    }

    /**
     * @dataProvider providerForIpOutsideRange
     *
     * @param $input
     * @param $networkRange
     */
    public function testIpsOutsideRangeShouldFail($input, $networkRange)
    {
        $rule = new Rule($networkRange);
        self::assertFalse($rule->validate($input));
        self::assertNotEmpty($rule->__toString());
    }

    /**
     * @return array
     */
    public function providerForPass(): array
    {
        return [
            ['127.0.0.1'],
        ];
    }

    /**
     * @return array
     */
    public function providerForFail(): array
    {
        return array(
            [null],
            [''],
            ['j'],
            [' '],
            ['Foo'],
            ['192.168.0.1', FILTER_FLAG_NO_PRIV_RANGE],
        );
    }

    /**
     * @return array
     */
    public function providerForComponentException(): array
    {
        return array(
            ['192.168'],
            ['asd'],
            ['192.168.0.0-192.168.0.256'],
            ['192.168.0.0-192.168.0.1/4'],
            ['192.168.0.256-192.168.0.255'],
            ['192.168.0/1'],
            ['192.168.2.0/256.256.256.256'],
            ['192.168.2.0/8.256.256.256'],
        );
    }

    /**
     * @return array
     */
    public function providerForIpBetweenRange(): array
    {
        return array(
            ['127.0.0.1', '127.*'],
            ['127.0.0.1', '127.0.*'],
            ['127.0.0.1', '127.0.0.*'],
            ['192.168.2.6', '192.168.*.6'],
            ['192.168.2.6', '192.*.2.6'],
            ['10.168.2.6', '*.168.2.6'],
            ['192.168.2.6', '192.168.*.*'],
            ['192.10.2.6', '192.*.*.*'],
            ['192.168.255.156', '*'],
            ['192.168.255.156', '*.*.*.*'],
            ['127.0.0.1', '127.0.0.0-127.0.0.255'],
            ['192.168.2.6', '192.168.0.0-192.168.255.255'],
            ['192.10.2.6', '192.0.0.0-192.255.255.255'],
            ['192.168.255.156', '0.0.0.0-255.255.255.255'],
            ['220.78.173.2', '220.78.168/21'],
            ['220.78.173.2', '220.78.168.0/21'],
            ['220.78.173.2', '220.78.168.0/255.255.248.0'],
        );
    }


    /**
     * @return array
     */
    public function providerForIpOutsideRange(): array
    {
        return array(
            ['127.0.0.1', '127.0.1.*'],
            ['192.168.2.6', '192.163.*.*'],
            ['192.10.2.6', '193.*.*.*'],
            ['127.0.0.1', '127.0.1.0-127.0.1.255'],
            ['192.168.2.6', '192.163.0.0-192.163.255.255'],
            ['192.10.2.6', '193.168.0.0-193.255.255.255'],
            ['220.78.176.1', '220.78.168/21'],
            ['220.78.176.2', '220.78.168.0/21'],
            ['220.78.176.3', '220.78.168.0/255.255.248.0'],
        );
    }
}
