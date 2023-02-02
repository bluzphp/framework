<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Unit\Validator\Rule;

use Bluz\Tests\Unit\Unit;
use Bluz\Validator\Rule\DateRule as Rule;

/**
 * Class DateTest
 *
 * @package Bluz\Tests\Validator\Rule
 */
class DateTest extends Unit
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

    public function testDateWithoutFormatShouldPass()
    {
        self::assertTrue($this->rule->validate('today'));
    }

    public function testDateTimeInstancesShouldAlwaysValidAndPass()
    {
        self::assertTrue($this->rule->validate(new \DateTime('today')));
    }

    /**
     * @dataProvider providerForPass
     *
     * @param $format
     * @param $date
     */
    public function testValidDateShouldPass($format, $date)
    {
        $rule = new Rule($format);
        self::assertTrue($rule->validate($date));
        self::assertNotEmpty($rule->__toString());
    }

    /**
     * @dataProvider providerForFail
     *
     * @param $format
     * @param $date
     */
    public function testInvalidateDateShouldFail($format, $date)
    {
        $rule = new Rule($format);
        self::assertFalse($rule->validate($date));
        self::assertNotEmpty($rule->__toString());
    }

    /**
     * @return array
     */
    public function providerForPass(): array
    {
        return [
            ['Y-m-d', '2009-09-09'],
            ['d/m/Y', '23/05/1987'],
        ];
    }

    /**
     * @return array
     */
    public function providerForFail(): array
    {
        return [
            [null, 'invalid date'],
            ['Y-m-d', '2009-09-00'],
            ['y-m-d', '2009-09-09'],
            ['y-m-d', new \stdClass()],
        ];
    }
}
