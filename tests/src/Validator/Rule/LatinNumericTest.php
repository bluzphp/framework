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
use Bluz\Validator\Rule\LatinNumeric;

/**
 * Class AlphaTest
 * @package Bluz\Tests\Validator\Rule
 */
class LatinNumericTest extends Tests\TestCase
{
    /**
     * @dataProvider providerForPass
     */
    public function testValidAlphanumericCharsShouldReturnTrue($validAlpha, $additional = '')
    {
        $validator = new LatinNumeric($additional);
        $this->assertTrue($validator->validate($validAlpha));
        $this->assertTrue($validator->assert($validAlpha));
    }

    /**
     * @dataProvider providerForFail
     * @expectedException \Bluz\Validator\Exception\ValidatorException
     */
    public function testInvalidAlphanumericCharsShouldReturnFalse($invalidAlpha, $additional = '')
    {
        $validator = new LatinNumeric($additional);
        $this->assertFalse($validator->validate($invalidAlpha));
        $this->assertFalse($validator->assert($invalidAlpha));
    }

    /**
     * @dataProvider providerForComponentException
     * @expectedException \Bluz\Validator\Exception\ComponentException
     */
    public function testInvalidConstructorParamsShouldThrowComponentException($additional)
    {
        new LatinNumeric($additional);
    }

    /**
     * @dataProvider providerAdditionalChars
     */
    public function testAdditionalCharsShouldBeRespected($additional, $query)
    {
        $validator = new LatinNumeric($additional);
        $this->assertTrue($validator->validate($query));
    }

    /**
     * Check templates
     */
    public function testTemplates()
    {
        $validator = new LatinNumeric();
        $this->assertNotEmpty($validator->__toString());

        $validator = new LatinNumeric('[]');
        $this->assertNotEmpty($validator->__toString());
    }

    /**
     * @return array
     */
    public function providerForPass()
    {
        return array(
            array(0),
            array(123456789),
            array('1234567890'),
            array('foobar'),
            array('foobar1234567890'),
            array('foobar_', '_'),
            array('google.com.ua', '.'),
            array('foobar foobar', ' ')
        );
    }

    /**
     * @return array
     */
    public function providerForFail()
    {
        return array(
            array(1e21),
            array('@#$'),
            array('_'),
            array('dgç'),
            array(null),
            array(new \stdClass),
            array(array()),
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
            array('!@#$%^&*(){} ', '!@#$%^&*(){} abc'),
            array('[]?+=/\\-_|"\',<>. ', "[]?+=/\\-_|\"',<>. abc"),
        );
    }
}
