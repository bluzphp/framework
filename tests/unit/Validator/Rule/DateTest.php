<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Validator\Rule;

use \DateTime;
use Bluz\Tests;
use Bluz\Validator\Rule\Date;

/**
 * Class DateTest
 * @package Bluz\Tests\Validator\Rule
 */
class DateTest extends Tests\TestCase
{
    /**
     * @var Date
     */
    protected $validator;

    /**
     * Setup validator instance
     */
    protected function setUp()
    {
        parent::setUp();
        $this->validator = new Date;
    }

    public function testDateWithoutFormatShouldValidate()
    {
        self::assertTrue($this->validator->validate('today'));
    }

    public function testDateTimeInstancesShouldAlwaysValidate()
    {
        self::assertTrue($this->validator->validate(new DateTime('today')));
    }

    /**
     * @dataProvider providerForPass
     * @param $format
     * @param $date
     */
    public function testValidDateShouldPass($format, $date)
    {
        $validator = new Date($format);
        self::assertTrue($validator->validate($date));
        self::assertNotEmpty($validator->__toString());
    }

    /**
     * @dataProvider providerForFail
     * @param $format
     * @param $date
     */
    public function testInvalidateDateShouldFail($format, $date)
    {
        $validator = new Date($format);
        self::assertFalse($validator->validate($date));
        self::assertNotEmpty($validator->__toString());
    }

    /**
     * @dataProvider providerForFail
     * @expectedException \Bluz\Validator\Exception\ValidatorException
     * @param $format
     * @param $date
     */
    public function testInvalidateDateThrowException($format, $date)
    {
        $validator = new Date($format);
        $validator->assert($date);
    }

    /**
     * @return array
     */
    public function providerForPass()
    {
        return array(
            ['Y-m-d', '2009-09-09'],
            ['d/m/Y', '23/05/1987'],
        );
    }

    /**
     * @return array
     */
    public function providerForFail()
    {
        return array(
            [null, 'invalid date'],
            ['Y-m-d', '2009-09-00'],
            ['y-m-d', '2009-09-09'],
            ['y-m-d', new \stdClass],
        );
    }
}
