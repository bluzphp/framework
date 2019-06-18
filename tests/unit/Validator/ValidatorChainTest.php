<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Validator;

use Bluz\Tests;
use Bluz\Validator\Exception\ValidatorException;
use Bluz\Validator\Rule\RuleInterface;
use Bluz\Validator\Validator;
use Bluz\Validator\ValidatorChain;

/**
 * Class ValidatorChainTest
 *
 * @package Bluz\Tests\Validator
 */
class ValidatorChainTest extends Tests\FrameworkTestCase
{
    public function testRunValidationWithValidDataShouldPass()
    {
        $validatorChain = Validator::alphaNumeric('_')->length(1, 15)->noWhitespace();
        self::assertTrue($validatorChain->validate('username'));
    }

    public function testEvalValidationWithValidDataShouldPass()
    {
        $validatorChain = Validator::alphaNumeric('_')->length(1, 15)->noWhitespace();
        self::assertTrue($validatorChain('username'));
        self::assertTrue($validatorChain('user_name'));
    }

    public function testRunValidationWithInvalidDataShouldFail()
    {
        $validatorChain = Validator::alphaNumeric('_')->length(1, 15)->noWhitespace();
        self::assertFalse($validatorChain->validate('invalid username'));
    }

    /**
     * Complex test with exception
     */
    public function testAssertInvalidDataShouldRaiseException()
    {
        $this->expectException(ValidatorException::class);
        Validator::alphaNumeric('_')->length(1, 15)->noWhitespace()->assert('invalid username');
    }

    public function testSetCustomDescriptionForCallbackRuleShouldUseItInChainDescription()
    {
        $chain = Validator::create()
            ->callback('is_int', 'it should be custom one')
            ->callback('is_numeric', 'it should be custom two')
        ;
        self::assertEqualsArray(
            ['it should be custom one', 'it should be custom two'],
            $chain->getDescription()
        );
    }

    public function testSetCustomDescriptionForRegexpRuleShouldUseItInChainDescription()
    {
        $chain = Validator::create()
            ->regexp('[0-9]+', 'it should be custom one')
            ->regexp('[a-z]+', 'it should be custom two')
        ;
        self::assertEqualsArray(
            ['it should be custom one', 'it should be custom two'],
            $chain->getDescription()
        );
    }

    public function testSetCustomDescriptionForRuleShouldUseItInChainDescription()
    {
        $chain = Validator::callback('is_int', 'it should be custom one')
            ->callback('is_numeric', 'it should be custom two');
        self::assertEqualsArray(
            ['it should be custom one', 'it should be custom two'],
            $chain->getDescription()
        );
    }

    public function testSetCustomDescriptionForSingleRuleShouldUseItAsErrorMessage()
    {
        try {
            Validator::callback('is_int', 'it should be custom')
                ->callback('is_numeric', 'it should be custom')
                ->assert('something');
        } catch (\Exception $e) {
            self::assertEquals('it should be custom', $e->getMessage());
        }
    }

    public function testSetCustomDescriptionForValidatorChainShouldUseItInChainDescription()
    {
        $chain = Validator::create()
            ->callback('is_int')
            ->callback('is_numeric')
            ->setDescription('it should be custom');

        self::assertEqualsArray(
            ['it should be custom'],
            $chain->getDescription()
        );
    }

    public function testSetCustomDescriptionForValidatorChainShouldUseItAsErrorMessage()
    {
        try {
            Validator::create()
                ->callback('is_int')
                ->callback('is_numeric')
                ->setDescription('it should be custom')
                ->assert('something');
        } catch (\Exception $e) {
            self::assertEquals('it should be custom', $e->getMessage());
        }
    }

    public function testSetCustomDescriptionForValidatorChain()
    {
        $validator = Validator::create()
            ->alphaNumeric('_')
            ->length(1, 15)
            ->noWhitespace();

        $ruleText = "must contain only Latin letters, digits and \"_\"\n"
            . "must have a length between \"1\" and \"15\"\n"
            . "must not contain whitespace";

        self::assertEquals($validator->__toString(), $ruleText);

        $customRuleText = "Username must contain only letters, digits and underscore, \n"
            . "must have a length between 1 and 15";

        $validator->setDescription($customRuleText);

        self::assertEquals($validator->__toString(), $customRuleText);

        self::assertFalse($validator->validate('user#name'));
        self::assertNotEmpty($validator->getError());
    }
}
