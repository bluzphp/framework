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
use Bluz\Validator\Rule\AlphaNumeric;

/**
 * Class AlphaNumericTest
 * @package Bluz\Tests\Validator\Rule
 */
class AlphaNumericTest extends Tests\TestCase
{
    /**
     * @dataProvider providerForPass
     */
    public function testValidAlphaNumericCharsShouldReturnTrue($validAlphaNumeric, $additional)
    {
        $validator = new AlphaNumeric($additional);
        $this->assertTrue($validator->validate($validAlphaNumeric));
        $this->assertTrue($validator->assert($validAlphaNumeric));
    }

    /**
     * @dataProvider providerForFail
     * @expectedException \Bluz\Validator\Exception\ValidatorException
     */
    public function testInvalidAlphaNumericCharsShouldReturnFalse($invalidAlphaNumeric, $additional)
    {
        $validator = new AlphaNumeric($additional);
        $this->assertFalse($validator->validate($invalidAlphaNumeric));
        $this->assertFalse($validator->assert($invalidAlphaNumeric));
    }

    /**
     * @dataProvider providerForComponentException
     * @expectedException \Bluz\Validator\Exception\ComponentException
     */
    public function testInvalidConstructorParamsShouldThrowComponentException($additional)
    {
        new AlphaNumeric($additional);
    }

    /**
     * @dataProvider providerAdditionalChars
     */
    public function testAdditionalCharsShouldBeRespected($additional, $query)
    {
        $validator = new AlphaNumeric($additional);
        $this->assertTrue($validator->validate($query));
    }

    /**
     * Check templates
     */
    public function testTemplates()
    {
        $validator = new AlphaNumeric();
        $this->assertNotEmpty($validator->__toString());

        $validator = new AlphaNumeric('[]');
        $this->assertNotEmpty($validator->__toString());
    }

    /**
     * @return array
     */
    public function providerForPass()
    {
        return array(
            array('', ''),
            array('foobar', ''),
            array('foobar', 'foobar'),
            array('0alg-anet0', '0-9'),
            array('1', ''),
            array("\t", ''),
            array("\n", ''),
            array('a', ''),
            array('foobar', ''),
            array('rubinho_', '_'),
            array('google.com', '.'),
            array('foobar foobar', ''),
            array("\nabc", ''),
            array("\tdef", ''),
            array("\nabc \t", ''),
            array(0, ''),
        );
    }

    /**
     * @return array
     */
    public function providerForFail()
    {
        return array(
            array('@#$', ''),
            array('_', ''),
            array('dgç', ''),
            array(1e21, ''), //evaluates to "1.0E+21"
            array(null, ''),
            array(new \stdClass, ''),
            array(array(), ''),
        );
    }

    /**
     * @return array
     */
    public function providerForComponentException()
    {
        return array(
            array(new \stdClass),
            array(array()),
            array(0x2)
        );
    }

    /**
     * @return array
     */
    public function providerAdditionalChars()
    {
        return array(
            array('!@#$%^&*(){}', '!@#$%^&*(){} abc 123'),
            array('[]?+=/\\-_|"\',<>.', "[]?+=/\\-_|\"',<>. \t \n abc 123"),
        );
    }
}
