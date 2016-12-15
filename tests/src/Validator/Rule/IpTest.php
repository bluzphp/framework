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
use Bluz\Validator\Rule\Ip;

/**
 * Class IpTest
 * @package Bluz\Tests\Validator\Rule
 */
class IpTest extends Tests\TestCase
{
    /**
     * @dataProvider providerForPass
     * @param $input
     * @param null $options
     */
    public function testValidIpsShouldReturnTrue($input, $options = null)
    {
        $validator = new Ip($options);
        self::assertTrue($validator->validate($input));
        self::assertTrue($validator->assert($input));
    }

    /**
     * @dataProvider providerForFail
     * @expectedException \Bluz\Validator\Exception\ValidatorException
     * @param $input
     * @param null $options
     */
    public function testInvalidIpsShouldReturnFalseAndThrowException($input, $options = null)
    {
        $validator = new Ip($options);
        self::assertFalse($validator->validate($input));
        self::assertNotEmpty($validator->__toString());
        self::assertFalse($validator->assert($input));
    }

    /**
     * @dataProvider providerForComponentException
     * @expectedException \Bluz\Validator\Exception\ComponentException
     * @param $range
     */
    public function testInvalidRangeShouldRaiseException($range)
    {
        new Ip($range);
    }

    /**
     * @dataProvider providerForIpBetweenRange
     * @param $input
     * @param $networkRange
     */
    public function testIpsBetweenRangeShouldReturnTrue($input, $networkRange)
    {
        $validator = new Ip($networkRange);
        self::assertTrue($validator->validate($input));
        self::assertTrue($validator->assert($input));
    }

    /**
     * @dataProvider providerForIpOutsideRange
     * @expectedException \Bluz\Validator\Exception\ValidatorException
     * @param $input
     * @param $networkRange
     */
    public function testIpsOutsideRangeShouldReturnFalse($input, $networkRange)
    {
        $validator = new Ip($networkRange);
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
            ['127.0.0.1'],
        );
    }

    /**
     * @return array
     */
    public function providerForFail()
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
    public function providerForComponentException()
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
    public function providerForIpBetweenRange()
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
    public function providerForIpOutsideRange()
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
