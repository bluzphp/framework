<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Validator\Rule;

use Bluz\Tests;
use Bluz\Validator\Rule\CallbackRule as Rule;

/**
 * Class CallbackTest
 *
 * @package Bluz\Tests\Validator\Rule
 */
class CallbackTest extends Tests\FrameworkTestCase
{
    /**
     * @var Rule
     */
    private $alwaysTrue;

    /**
     * @var Rule
     */
    private $alwaysFalse;

    /**
     * Setup Callbacks
     */
    public function setUp(): void
    {
        $this->alwaysTrue = new Rule(
            function () {
                return true;
            }
        );
        $this->alwaysFalse = new Rule(
            function () {
                return false;
            }
        );
    }

    public function testCallbackValidatorShouldPassIfCallbackReturnsTrue()
    {
        self::assertTrue($this->alwaysTrue->validate('foo-bar'));
        self::assertNotEmpty($this->alwaysFalse->__toString());
    }

    public function testCallbackValidatorShouldFailIfCallbackReturnsFalse()
    {
        self::assertFalse($this->alwaysFalse->validate('foo-bar'));
        self::assertNotEmpty($this->alwaysFalse->__toString());
    }

    public function testCallbackValidatorShouldAcceptArrayCallbackDefinitions()
    {
        $rule = new Rule([$this, 'thisIsASampleCallbackUsedInsideThisTest']);
        self::assertTrue($rule->validate('test'));
    }

    public function testCallbackValidatorShouldAcceptFunctionNamesAsString()
    {
        $rule = new Rule('is_string');
        self::assertTrue($rule->validate('test'));
    }

    public function testInvalidCallbacksShouldRaiseComponentExceptionUponInstantiation()
    {
        $this->expectException(\TypeError::class);
        new Rule(new \stdClass());
    }

    /**
     * @return bool
     */
    public function thisIsASampleCallbackUsedInsideThisTest()
    {
        return true;
    }
}
