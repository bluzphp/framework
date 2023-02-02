<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Unit\Validator;

use Bluz\Tests\Unit\Unit;
use Bluz\Validator\Exception\ComponentException;
use Bluz\Validator\Exception\ValidatorException;
use Bluz\Validator\Rule\RuleInterface;
use Bluz\Validator\Validator;
use Bluz\Validator\ValidatorChain;

/**
 * Class ValidatorTest
 *
 * @package Bluz\Tests\Validator
 */
class ValidatorTest extends Unit
{
    /**
     * Create new instance of validator
     */
    public function testStaticCreateShouldReturnNewValidatorChain()
    {
        self::assertInstanceOf(ValidatorChain::class, Validator::create());
    }

    /**
     * Every static call of exist Rule should be return a new instance of Rule
     */
    public function testStaticCallsShouldReturnNewValidatorChain()
    {
        self::assertInstanceOf(
            ValidatorChain::class,
            Validator::array(
                function () {
                    return true;
                }
            )
        );
        self::assertInstanceOf(ValidatorChain::class, Validator::string());
        self::assertInstanceOf(ValidatorChain::class, Validator::notEmpty());
    }

    public function testStaticCallShouldCreateValidRule()
    {
        $validator = Validator::callback('is_int');

        self::assertTrue($validator->validate(42));
    }

    public function testStaticCallShouldAllowInvokeIt()
    {
        self::assertTrue(Validator::callback('is_int')(42));
    }

    public function testStaticAddRuleNamespace()
    {
        Validator::addRuleNamespace('\\Bluz\\Tests\\Fixtures\\Validator\\Rule\\');
        self::assertInstanceOf(ValidatorChain::class, Validator::custom());
    }

    public function testInvalidRuleClassShouldRaiseComponentException()
    {
        $this->expectException(ComponentException::class);
        Validator::iDoNotExistSoIShouldThrowException();
    }
}
