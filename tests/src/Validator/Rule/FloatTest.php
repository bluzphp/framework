<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Validator\Rule;

use Bluz\Tests;
use Bluz\Validator\Rule\FloatRule;

/**
 * Class FloatTest
 * @package Bluz\Tests\Validator\Rule
 */
class FloatTest extends Tests\TestCase
{
    /**
     * @var \Bluz\Validator\Rule\FloatRule
     */
    protected $validator;

    /**
     * Setup validator instance
     */
    protected function setUp()
    {
        parent::setUp();
        $this->validator = new FloatRule();
    }

    /**
     * @dataProvider providerForPass
     */
    public function testFloatNumbersShouldPass($input)
    {
        $this->assertTrue($this->validator->validate($input));
        $this->assertTrue($this->validator->assert($input));
    }

    /**
     * @dataProvider providerForFail
     * @expectedException \Bluz\Validator\Exception\ValidatorException
     */
    public function testNotFloatNumbersShouldFail($input)
    {
        $this->assertFalse($this->validator->validate($input));
        $this->assertFalse($this->validator->assert($input));
    }

    /**
     * @return array
     */
    public function providerForPass()
    {
        return array(
            array(165),
            array(1),
            array(0),
            array(0.0),
            array('1'),
            array('19347e12'),
            array(165.0),
            array('165.7'),
            array(1e12),
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
            array('a'),
            array(' '),
            array('Foo'),
        );
    }
}
