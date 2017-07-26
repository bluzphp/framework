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
    public function testSetCustomDescriptionForRuleShouldUseItInChainDescription()
    {
        $chain = Validator::create()
            ->addRule(
                Validator::callback('is_int')->setDescription('it should be custom one')
            )->addRule(
                Validator::callback('is_numeric')->setDescription('it should be custom two')
            );
        self::assertEqualsArray(
            ['it should be custom one', 'it should be custom two'],
            $chain->getDescription()
        );
    }

    public function testSetCustomDescriptionForSingleRuleShouldUseItAsErrorMessage()
    {
        try {
            Validator::create()
                ->addRule(
                    Validator::callback('is_int')->setDescription('it should be custom')
                )->addRule(
                    Validator::callback('is_numeric')->setDescription('it should be custom')
                )
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

        $ruleText = "must contain only letters, digits and \"_\"\n"
            . "must have a length between 1 and 15\n"
            . "must not contain whitespace";

        self::assertEquals($validator->__toString(), $ruleText);

        $customRuleText = "Username must contain only letters, digits and underscore, \n"
            . "must have a length between 1 and 15";

        $validator->setDescription($customRuleText);

        self::assertEquals($validator->__toString(), $customRuleText);

        self::assertFalse($validator->validate('user#name'));
        self::assertNotEmpty($validator->getError());
    }

    public function testRunValidationWithValidDataShouldPass()
    {
        $validatorChain = Validator::create()->alphaNumeric('_')->length(1, 15)->noWhitespace();
        self::assertTrue($validatorChain->validate('username'));
    }

    public function testRunValidationWithInvalidDataShouldFail()
    {
        $validatorChain = Validator::create()->alphaNumeric('_')->length(1, 15)->noWhitespace();
        self::assertFalse($validatorChain->validate('invalid username'));
    }

    /**
     * Complex test with exception
     *
     * @expectedException \Bluz\Validator\Exception\ValidatorException
     */
    public function testAssertInvalidDataShouldRaiseException()
    {
        Validator::create()->alphaNumeric('_')->length(1, 15)->noWhitespace()->assert('invalid username');
    }
}
