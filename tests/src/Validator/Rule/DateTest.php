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
        $this->assertTrue($this->validator->validate('today'));
    }

    public function testDateTimeInstancesShouldAlwaysValidate()
    {
        $this->assertTrue($this->validator->validate(new DateTime('today')));
    }

    /**
     * @dataProvider providerForPass
     */
    public function testValidDateShouldPass($format, $date)
    {
        $validator = new Date($format);
        $this->assertTrue($validator->validate($date));
        $this->assertNotEmpty($validator->__toString());
    }

    /**
     * @dataProvider providerForFail
     */
    public function testInvalidateDateShouldFail($format, $date)
    {
        $validator = new Date($format);
        $this->assertFalse($validator->validate($date));
        $this->assertNotEmpty($validator->__toString());
    }

    /**
     * @dataProvider providerForFail
     * @expectedException \Bluz\Validator\Exception\ValidatorException
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
            array('Y-m-d', '2009-09-09'),
            array('d/m/Y', '23/05/1987'),
        );
    }

    /**
     * @return array
     */
    public function providerForFail()
    {
        return array(
            array(null, 'invalid date'),
            array('Y-m-d', '2009-09-00'),
            array('y-m-d', '2009-09-09'),
            array('y-m-d', new \stdClass),
        );
    }
}
