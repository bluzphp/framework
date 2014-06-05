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
use Bluz\Validator\Rule\NoWhitespace;

/**
 * Class NoWhitespaceTest
 * @package Bluz\Tests\Validator\Rule
 */
class NoWhitespaceTest extends Tests\TestCase
{
    /**
     * @var NoWhitespace
     */
    protected $validator;

    /**
     * Setup validator instance
     */
    protected function setUp()
    {
        parent::setUp();
        $this->validator = new NoWhitespace;
    }

    /**
     * @dataProvider providerForPass
     */
    public function testStringWithNoWhitespaceShouldPass($input)
    {
        $this->assertTrue($this->validator->validate($input));
    }

    /**
     * @dataProvider providerForFail
     */
    public function testStringWithWhitespaceShouldFail($input)
    {
        $this->assertFalse($this->validator->validate($input));
    }

    /**
     * @return array
     */
    public function providerForPass()
    {
        return array(
            array(''),
            array(0),
            array('wpoiur'),
            array('Foo'),
        );
    }

    /**
     * @return array
     */
    public function providerForFail()
    {
        return array(
            array(' '),
            array('w poiur'),
            array('      '),
            array("Foo\nBar"),
            array("Foo\tBar"),
        );
    }
}
