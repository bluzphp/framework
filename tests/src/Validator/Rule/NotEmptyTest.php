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
use Bluz\Validator\Rule\NotEmpty;

/**
 * Class NotEmptyTest
 * @package Bluz\Tests\Validator\Rule
 */
class NotEmptyTest extends Tests\TestCase
{
    /**
     * @var NotEmpty
     */
    protected $validator;

    /**
     * Setup validator instance
     */
    protected function setUp()
    {
        parent::setUp();
        $this->validator = new NotEmpty;
    }

    /**
     * @dataProvider providerForPass
     */
    public function testStringNotEmpty($input)
    {
        $this->assertTrue($this->validator->validate($input));
    }

    /**
     * @dataProvider providerForFail
     */
    public function testStringEmpty($input)
    {
        $this->assertFalse($this->validator->validate($input));
    }

    /**
     * @return array
     */
    public function providerForPass()
    {
        return array(
            array(1),
            array(' oi'),
            array(array(5)),
            array(array(0)),
            array(new \stdClass)
        );
    }

    /**
     * @return array
     */
    public function providerForFail()
    {
        return array(
            array(''),
            array('    '),
            array("\n"),
            array(false),
            array(null),
            array(array())
        );
    }
}
