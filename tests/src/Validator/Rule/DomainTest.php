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
            ['domain.local'],
//            ['google.com', true],
            ['example.com'],
            ['xn--bcher-kva.com'],
            ['example-hyphen.com'],
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
//            ['domain.local', true],
            ['-example-invalid.com'],
            ['example.invalid.-com'],
//            ['1.2.3.256', true],
//            ['1.2.3.4', true],
        );
    }
}
