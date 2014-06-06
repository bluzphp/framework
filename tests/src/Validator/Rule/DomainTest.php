<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Respect\Validation\Rules;

use Bluz\Tests;
use Bluz\Validator\Rule\Domain;

/**
 * Class DomainTest
 * @package Respect\Validation\Rules
 */
class DomainTest extends Tests\TestCase
{
    /**
     * @dataProvider providerForPass
     */
    public function testValidDomainsShouldReturnTrue($input, $checkDns = false)
    {
        $validator = new Domain($checkDns);
        $this->assertTrue($validator->validate($input));
        $this->assertTrue($validator->assert($input));
    }

    /**
     * @dataProvider providerForFail
     * @expectedException \Bluz\Validator\Exception\ValidatorException
     */
    public function testValidDomainsShouldReturnFalse($input, $checkDns = false)
    {
        $validator = new Domain($checkDns);
        $this->assertFalse($validator->validate($input));
        $this->assertFalse($validator->assert($input));
    }

    /**
     * @return array
     */
    public function providerForPass()
    {
        return array(
            array('domain.local'),
            array('google.com', true),
            array('example.com'),
            array('xn--bcher-kva.com'),
            array('example-hyphen.com'),
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
            array('domain.local', true),
            array('-example-invalid.com'),
            array('example.invalid.-com'),
            array('1.2.3.256', true),
            array('1.2.3.4', true),
        );
    }
}
