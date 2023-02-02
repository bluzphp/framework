<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Unit\Validator\Rule;

use Bluz\Tests\Unit\Unit;
use Bluz\Validator\Rule\DomainRule as Rule;

/**
 * Class DomainTest
 *
 * @package Respect\Validation\Rules
 */
class DomainTest extends Unit
{
    /**
     * @dataProvider providerForPass
     * @param string $input
     */
    public function testValidDomainsShouldPass($input)
    {
        $rule = new Rule();
        self::assertTrue($rule->validate($input));
        self::assertNotEmpty($rule->__toString());
    }

    /**
     * @dataProvider providerForFail
     * @param string $input
     */
    public function testValidDomainsShouldFail($input)
    {
        $rule = new Rule();
        self::assertFalse($rule->validate($input));
        self::assertNotEmpty($rule->__toString());
    }

    /**
     * @dataProvider providerRealDomainForPass
     * @param string $input
     */
    public function testValidDomainWithDomainCheck($input)
    {
        self::markTestIncomplete('To slow to check it every time');

//        $rule = new Rule(true);
//        self::assertTrue($rule->validate($input));
//        self::assertNotEmpty($rule->__toString());
    }

    /**
     * @dataProvider providerRealDomainForFail
     * @param string $input
     */
    public function testInvalidDomainWithDomainCheck($input)
    {
        self::markTestIncomplete('To slow to check it every time');

//        $rule = new Rule(true);
//        self::assertFalse($rule->validate($input));
//        self::assertNotEmpty($rule->__toString());
    }

    /**
     * @return array
     */
    public function providerForPass(): array
    {
        return [
            ['domain.local'],
            ['example.com'],
            ['xn--bcher-kva.com'],
            ['example-hyphen.com'],
        ];
    }

    /**
     * @return array
     */
    public function providerForFail(): array
    {
        return [
            [null],
            [''],
            ['-example-invalid.com'],
            ['example.invalid.-com'],
        ];
    }

    /**
     * @return array
     */
    public function providerRealDomainForPass(): array
    {
        return [
            ['google.com'],
        ];
    }

    /**
     * @return array
     */
    public function providerRealDomainForFail(): array
    {
        return [
            ['domain.local'],
            ['1.2.3.4'],
        ];
    }
}
