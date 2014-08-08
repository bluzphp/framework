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
use Bluz\Validator\Rule\Callback;

/**
 * Class CallbackTest
 * @package Bluz\Tests\Validator\Rule
 */
class CallbackTest extends Tests\TestCase
{
    /**
     * @var Callback
     */
    private $alwaysTrue;

    /**
     * @var Callback
     */
    private $alwaysFalse;

    /**
     * Setup Callbacks
     */
    public function setUp()
    {
        parent::setUp();
        $this->alwaysTrue = new Callback(function () {
            return true;
        });
        $this->alwaysFalse = new Callback(function () {
            return false;
        });
    }

    public function testCallbackValidatorShouldReturnTrueIfCallbackReturnsTrue()
    {
        $this->assertTrue($this->alwaysTrue->validate('foo-bar'));
    }

    public function testCallbackValidatorShouldReturnFalseIfCallbackReturnsFalse()
    {
        $this->assertFalse($this->alwaysFalse->validate('foo-bar'));
    }

    public function testCallbackValidatorShouldAcceptArrayCallbackDefinitions()
    {
        $v = new Callback(array($this, 'thisIsASampleCallbackUsedInsideThisTest'));
        $this->assertTrue($v->validate('test'));
    }

    public function testCallbackValidatorShouldAcceptFunctionNamesAsString()
    {
        $v = new Callback('is_string');
        $this->assertTrue($v->validate('test'));
    }

    /**
     * @expectedException \Bluz\Validator\Exception\ComponentException
     */
    public function testInvalidCallbacksShouldRaiseComponentExceptionUponInstantiation()
    {
        new Callback(new \stdClass);
    }

    /**
     * @return bool
     */
    public function thisIsASampleCallbackUsedInsideThisTest()
    {
        return true;
    }
}
