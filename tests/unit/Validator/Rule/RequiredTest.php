<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Validator\Rule;

use Bluz\Tests;
use Bluz\Validator\Rule\RequiredRule as Rule;

/**
 * Class RequiredTest
 *
 * @package Bluz\Tests\Validator\Rule
 */
class RequiredTest extends Tests\FrameworkTestCase
{
    /**
     * @var Rule
     */
    protected $rule;

    /**
     * Setup validator instance
     */
    protected function setUp(): void
    {
        $this->rule = new Rule();
    }

    /**
     * @dataProvider providerForPass
     *
     * @param $input
     */
    public function testRequiredShouldPass($input)
    {
        self::assertTrue($this->rule->validate($input));
        self::assertNotEmpty($this->rule->__toString());
    }

    /**
     * @dataProvider providerForFail
     *
     * @param $input
     */
    public function testNotExistsShouldFail($input)
    {
        self::assertFalse($this->rule->validate($input));
        self::assertNotEmpty($this->rule->__toString());
    }

    /**
     * @return array
     */
    public function providerForPass(): array
    {
        return array(
            [1],
            ['foo'],
            [[5]],
            [[0]],
            [new \stdClass()]
        );
    }

    /**
     * @return array
     */
    public function providerForFail(): array
    {
        return [
            [''],
            [false],
            [null]
        ];
    }
}
