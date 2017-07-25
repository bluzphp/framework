<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Respect\Validation\Rules;

use Bluz\Tests;
use Bluz\Validator\Rule\DomainRule as Rule;

/**
 * Class DomainTest
 *
 * @package Respect\Validation\Rules
 */
class DomainTest extends Tests\FrameworkTestCase
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

        return;
        $rule = new Rule(true);
        self::assertTrue($rule->validate($input));
        self::assertNotEmpty($rule->__toString());
    }

    /**
     * @dataProvider providerRealDomainForFail
     * @param string $input
     */
    public function testInvalidDomainWithDomainCheck($input)
    {
        self::markTestIncomplete('To slow to check it every time');

        return;
        $rule = new Rule(true);
        self::assertFalse($rule->validate($input));
        self::assertNotEmpty($rule->__toString());
    }

    /**
     * @return array
     */
    public function providerForPass() : array
    {
        return array(
            ['domain.local'],
            ['example.com'],
            ['xn--bcher-kva.com'],
            ['example-hyphen.com'],
        );
    }

    /**
     * @return array
     */
    public function providerForFail() : array
    {
        return array(
            [null],
            [''],
            ['-example-invalid.com'],
            ['example.invalid.-com'],
        );
    }

    /**
     * @return array
     */
    public function providerRealDomainForPass() : array
    {
        return array(
            ['google.com'],
        );
    }

    /**
     * @return array
     */
    public function providerRealDomainForFail() : array
    {
        return array(
            ['domain.local'],
            ['1.2.3.4'],
        );
    }
}
