<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Validator\Rule;

use Bluz\Tests;
use Bluz\Validator\Rule\NotEmptyRule as Rule;

/**
 * Class NotEmptyTest
 *
 * @package Bluz\Tests\Validator\Rule
 */
class NotEmptyTest extends Tests\FrameworkTestCase
{
    /**
     * @var Rule
     */
    protected $rule;

    /**
     * Setup validator instance
     */
    protected function setUp()
    {
        $this->rule = new Rule;
    }

    /**
     * @dataProvider providerForPass
     *
     * @param $input
     */
    public function testStringNotEmptyShouldPass($input)
    {
        self::assertTrue($this->rule->validate($input));
        self::assertNotEmpty($this->rule->__toString());
    }

    /**
     * @dataProvider providerForFail
     *
     * @param $input
     */
    public function testStringIsEmptyShouldFail($input)
    {
        self::assertFalse($this->rule->validate($input));
        self::assertNotEmpty($this->rule->__toString());
    }

    /**
     * @return array
     */
    public function providerForPass() : array
    {
        return array(
            [1],
            [' oi'],
            [[5]],
            [[0]],
            [new \stdClass]
        );
    }

    /**
     * @return array
     */
    public function providerForFail() : array
    {
        return array(
            [''],
            ['    '],
            ["\n"],
            [false],
            [null],
            [[]]
        );
    }
}
