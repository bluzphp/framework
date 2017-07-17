<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */

namespace Bluz\Tests\Validator\Rule;

use Bluz\Tests;
use Bluz\Validator\Rule\Callback;

/**
 * Class CallbackTest
 *
 * @package Bluz\Tests\Validator\Rule
 */
class CallbackTest extends Tests\FrameworkTestCase
{
    /**
     * @var \Bluz\Validator\Rule\Callback
     */
    private $alwaysTrue;

    /**
     * @var \Bluz\Validator\Rule\Callback
     */
    private $alwaysFalse;

    /**
     * Setup Callbacks
     */
    public function setUp()
    {
        parent::setUp();
        $this->alwaysTrue = new Callback(
            function () {
                return true;
            }
        );
        $this->alwaysFalse = new Callback(
            function () {
                return false;
            }
        );
    }

    public function testCallbackValidatorShouldReturnTrueIfCallbackReturnsTrue()
    {
        self::assertTrue($this->alwaysTrue->validate('foo-bar'));
    }

    public function testCallbackValidatorShouldReturnFalseIfCallbackReturnsFalse()
    {
        self::assertFalse($this->alwaysFalse->validate('foo-bar'));
    }

    public function testCallbackValidatorShouldAcceptArrayCallbackDefinitions()
    {
        $v = new Callback([$this, 'thisIsASampleCallbackUsedInsideThisTest']);
        self::assertTrue($v->validate('test'));
    }

    public function testCallbackValidatorShouldAcceptFunctionNamesAsString()
    {
        $v = new Callback('is_string');
        self::assertTrue($v->validate('test'));
    }

    /**
     * @expectedException \TypeError
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
