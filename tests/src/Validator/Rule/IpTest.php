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
     */
    public function testValidIpsShouldReturnTrue($input, $options = null)
    {
        $validator = new Ip($options);
        $this->assertTrue($validator->validate($input));
        $this->assertTrue($validator->assert($input));
    }

    /**
     * @dataProvider providerForFail
     * @expectedException \Bluz\Validator\Exception\ValidatorException
     */
    public function testInvalidIpsShouldReturnFalseAndThrowException($input, $options = null)
    {
        $validator = new Ip($options);
        $this->assertFalse($validator->validate($input));
        $this->assertNotEmpty($validator->__toString());
        $this->assertFalse($validator->assert($input));
    }

    /**
     * @dataProvider providerForComponentException
     * @expectedException \Bluz\Validator\Exception\ComponentException
     */
    public function testInvalidRangeShouldRaiseException($range)
    {
        new Ip($range);
    }

    /**
     * @dataProvider providerForIpBetweenRange
     */
    public function testIpsBetweenRangeShouldReturnTrue($input, $networkRange)
    {
        $validator = new Ip($networkRange);
        $this->assertTrue($validator->validate($input));
        $this->assertTrue($validator->assert($input));
    }

    /**
     * @dataProvider providerForIpOutsideRange
     * @expectedException \Bluz\Validator\Exception\ValidatorException
     */
    public function testIpsOutsideRangeShouldReturnFalse($input, $networkRange)
    {
        $validator = new Ip($networkRange);
        $this->assertFalse($validator->validate($input));
        $this->assertNotEmpty($validator->__toString());
        $this->assertFalse($validator->assert($input));
    }

    /**
     * @return array
     */
    public function providerForPass()
    {
        return array(
            array('127.0.0.1'),
        );
    }

    /**
     * @return array
     */
    public function providerForFail()
    {
        return array(
            array(null),
            array(''),
            array('j'),
            array(' '),
            array('Foo'),
            array('192.168.0.1', FILTER_FLAG_NO_PRIV_RANGE),
        );
    }

    /**
     * @return array
     */
    public function providerForComponentException()
    {
        return array(
            array('192.168'),
            array('asd'),
            array('192.168.0.0-192.168.0.256'),
            array('192.168.0.0-192.168.0.1/4'),
            array('192.168.0.256-192.168.0.255'),
            array('192.168.0/1'),
            array('192.168.2.0/256.256.256.256'),
            array('192.168.2.0/8.256.256.256'),
        );
    }

    /**
     * @return array
     */
    public function providerForIpBetweenRange()
    {
        return array(
            array('127.0.0.1', '127.*'),
            array('127.0.0.1', '127.0.*'),
            array('127.0.0.1', '127.0.0.*'),
            array('192.168.2.6', '192.168.*.6'),
            array('192.168.2.6', '192.*.2.6'),
            array('10.168.2.6', '*.168.2.6'),
            array('192.168.2.6', '192.168.*.*'),
            array('192.10.2.6', '192.*.*.*'),
            array('192.168.255.156', '*'),
            array('192.168.255.156', '*.*.*.*'),
            array('127.0.0.1', '127.0.0.0-127.0.0.255'),
            array('192.168.2.6', '192.168.0.0-192.168.255.255'),
            array('192.10.2.6', '192.0.0.0-192.255.255.255'),
            array('192.168.255.156', '0.0.0.0-255.255.255.255'),
            array('220.78.173.2', '220.78.168/21'),
            array('220.78.173.2', '220.78.168.0/21'),
            array('220.78.173.2', '220.78.168.0/255.255.248.0'),
        );
    }


    /**
     * @return array
     */
    public function providerForIpOutsideRange()
    {
        return array(
            array('127.0.0.1', '127.0.1.*'),
            array('192.168.2.6', '192.163.*.*'),
            array('192.10.2.6', '193.*.*.*'),
            array('127.0.0.1', '127.0.1.0-127.0.1.255'),
            array('192.168.2.6', '192.163.0.0-192.163.255.255'),
            array('192.10.2.6', '193.168.0.0-193.255.255.255'),
            array('220.78.176.1', '220.78.168/21'),
            array('220.78.176.2', '220.78.168.0/21'),
            array('220.78.176.3', '220.78.168.0/255.255.248.0'),
        );
    }
}
