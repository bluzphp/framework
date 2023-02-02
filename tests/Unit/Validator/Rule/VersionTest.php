<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Unit\Validator\Rule;

use Bluz\Tests\Unit\Unit;
use Bluz\Validator\Exception\ValidatorException;
use Bluz\Validator\Rule\VersionRule as Rule;

/**
 * Class VersionTest
 *
 * @package Bluz\Tests\Validator\Rule
 */
class VersionTest extends Unit
{
    /**
     * @var Rule
     */
    protected $rule;

    protected function setUp(): void
    {
        $this->rule = new Rule();
    }

    /**
     * @dataProvider providerForPass
     *
     * @param $input
     */
    public function testValidVersionNumberShouldPass($input)
    {
        $rule = $this->rule;
        self::assertTrue($rule($input));
        self::assertTrue($this->rule->validate($input));
        self::assertNotEmpty($this->rule->__toString());
    }

    /**
     * @dataProvider providerForFail
     *
     * @param $input
     */
    public function testInvalidVersionNumberShouldFail($input)
    {
        $rule = $this->rule;
        self::assertFalse($rule($input));
        self::assertFalse($this->rule->validate($input));
        self::assertNotEmpty($this->rule->__toString());
    }

    /**
     * @dataProvider providerForFail
     *
     * @param $input
     */
    public function testInvalidVersionNumberShouldThrowException($input)
    {
        $this->expectException(ValidatorException::class);
        $this->rule->assert($input);
    }

    /**
     * @return array
     */
    public function providerForPass(): array
    {
        return [
            ['1.0.0'],
            ['1.0.0-alpha'],
            ['1.0.0-alpha.1'],
            ['1.0.0-0.3.7'],
            ['1.0.0-x.7.z.92'],
            ['1.3.7+build.2.b8f12d7'],
            ['1.3.7-rc.1'],
        ];
    }

    /**
     * @return array
     */
    public function providerForFail(): array
    {
        return [
            [''],
            ['1.3.7--'],
            ['1.3.7++'],
            ['foo'],
            ['1.2.3.4'],
            ['1.2.3.4-beta'],
            ['beta'],
        ];
    }
}
