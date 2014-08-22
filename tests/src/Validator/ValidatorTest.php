<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Validator;

use Bluz\Tests;
use Bluz\Validator\Exception\ValidatorException;
use Bluz\Validator\Validator;

/**
 * Class ValidatorTest
 * @package Bluz\Tests\Validator
 */
class ValidatorTest extends Tests\TestCase
{
    /**
     * Create new instance of validator
     */
    public function testStaticCreateShouldReturnNewValidator()
    {
        $this->assertInstanceOf('Bluz\Validator\Validator', Validator::create());
    }

    /**
     * Every static call of exist Rule should be return a new instance of Validator
     */
    public function testStaticCallsShouldReturnNewValidator()
    {
        $this->assertInstanceOf('Bluz\Validator\Validator', Validator::string());
        $this->assertInstanceOf('Bluz\Validator\Validator', Validator::notEmpty());
    }

    /**
     * @expectedException \Bluz\Validator\Exception\ComponentException
     */
    public function testInvalidRuleClassShouldThrowComponentException()
    {
        Validator::iDoNotExistSoIShouldThrowException();
    }

    /**
     * Setup custom error text for one rule in chain
     */
    public function testSetCustomErrorTextForSingleValidatorShouldUseItAsErrorMessage()
    {
        try {
            Validator::callback('is_int')
                ->setName('Input')
                ->setError('"{{name}}" is not numeric, is equal "{{input}}"')
                ->assert('something');
        } catch (\Exception $e) {
            $this->assertEquals('"Input" is not numeric, is equal "something"', $e->getMessage());
        }
    }

    /**
     * Setup custom error text for one rule in chain
     */
    public function testValidRules()
    {
        $validator = Validator::callback('is_int');

        $this->assertTrue($validator->validate(42));
        $this->assertTrue($validator->assert(42));
    }

    /**
     * Setup custom error text for many rules in chain
     */
    public function testSetCustomErrorTextForValidatorChainShouldUseItAsErrorMessage()
    {
        try {
            Validator::callback('is_int')
                ->callback('is_numeric')
                ->setName('Input')
                ->setError('"{{name}}" is not numeric, is equal "{{input}}"')
                ->assert('something');
        } catch (\Exception $e) {
            $this->assertEquals('"Input" is not numeric, is equal "something"', $e->getMessage());
        }
    }

    /**
     * Complex test
     */
    public function testErrorTextValidationInComplex()
    {
        $validator = Validator::create()
            ->setName('username')
            ->alphaNumeric('_')
            ->length(1, 15)
            ->noWhitespace();

        $ruleText = "username must contain only letters, digits and \"_\"\n"
            . "username must have a length between 1 and 15\n"
            . "username must not contain whitespace";

        $this->assertEquals($validator->__toString(), $ruleText);

        $customRuleText = "Username must contain only letters, digits and underscore, \n"
            . "must have a length between 1 and 15";

        $validator->setError($customRuleText);

        $this->assertEquals($validator->__toString(), $customRuleText);

        $this->assertFalse($validator->validate('user#name'));
        $this->assertFalse($validator('user#name'));

        $this->assertCount(1, $validator->getErrors());
    }

    /**
     * Complex test with exception
     *
     * @expectedException \Bluz\Validator\Exception\ValidatorException
     */
    public function testAssertComplexRuleValidation()
    {
        $validator = Validator::alphaNumeric('_')->length(1, 15)->noWhitespace();

        $validator->assert('invalid user name');
    }

    /**
     * Test ValidatorException
     * @expectedException \Bluz\Validator\Exception\ValidatorException
     */
    public function testValidatorException()
    {
        $exception = ValidatorException::exception('foo', 'bar');

        $this->assertEqualsArray(['foo' => 'bar'], $exception->getErrors());

        throw $exception;
    }
}
