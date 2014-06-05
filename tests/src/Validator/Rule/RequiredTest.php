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
use Bluz\Validator\Rule\Required;

/**
 * Class RequiredTest
 * @package Bluz\Tests\Validator\Rule
 */
class RequiredTest extends Tests\TestCase
{
    /**
     * @var Required
     */
    protected $validator;

    /**
     * Setup validator instance
     */
    protected function setUp()
    {
        parent::setUp();
        $this->validator = new Required;
    }

    /**
     * @dataProvider providerForRequired
     */
    public function testRequired($input)
    {
        $this->assertTrue($this->validator->validate($input));
    }

    /**
     * @dataProvider providerForNotExist
     */
    public function testNotExists($input)
    {
        $this->assertFalse($this->validator->validate($input));
    }

    /**
     * @return array
     */
    public function providerForRequired()
    {
        return array(
            array(1),
            array('foo'),
            array(array(5)),
            array(array(0)),
            array(new \stdClass)
        );
    }

    /**
     * @return array
     */
    public function providerForNotExist()
    {
        return array(
            array(''),
            array(false),
            array(null)
        );
    }
}
