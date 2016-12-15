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
use Bluz\Validator\Rule\Numeric;
use Bluz\Validator\Validator;
use Bluz\Validator\ValidatorBuilder;

/**
 * Class ValidatorBuilderTest
 * @package Bluz\Tests\Validator
 */
class ValidatorBuilderTest extends Tests\TestCase
{
    /**
     * Setup simple builder
     */
    public function testValidatorBuilderForOneRule()
    {
        $validator = new ValidatorBuilder();
        try {
            $validator->add(
                'some',
                Validator::create()
                    ->callback('is_int')
                    ->setError('"{{name}}" is not numeric, is equal "{{input}}"')
            );
            $validator->assert(['some' => 'something']);
        } catch (ValidatorException $e) {
            self::assertEquals('Invalid Arguments', $e->getMessage());
            self::assertArrayHasKey('some', $e->getErrors());
        }
    }

    /**
     * Setup simple builder for array
     */
    public function testValidatorBuilderForOneRuleForArray()
    {
        $validator = new ValidatorBuilder();
        try {
            $validator->add(
                'some',
                Validator::create()
                    ->arrayInput(new Numeric())
                    ->setError('"{{name}}" is not numeric, is equal "{{input}}"')
            );
            $validator->assert(['some' => ['something']]);
        } catch (ValidatorException $e) {
            self::assertEquals('Invalid Arguments', $e->getMessage());
            self::assertArrayHasKey('some', $e->getErrors());
        }
    }
    /**
     * Setup multi builder
     */
    public function testValidatorBuilderForRuleSet()
    {
        $validator = new ValidatorBuilder();
        try {
            $validator->add(
                'foo',
                Validator::required(),
                Validator::callback('is_int')
            );
            $validator->add(
                'bar',
                Validator::required(),
                Validator::callback('is_int')
            );
            $validator->add(
                'quz',
                Validator::required(),
                Validator::callback('is_int')
            );
            $validator->assert(['foo' => 'something']);
        } catch (ValidatorException $e) {
            self::assertEquals('Invalid Arguments', $e->getMessage());

            $errors = $validator->getErrors();

            self::assertArrayHasKey('foo', $errors);
            self::assertArrayHasKey('bar', $errors);
        }
    }

    /**
     * Setup multi builder for object
     */
    public function testValidatorBuilderForRuleSetAndObject()
    {
        $object = new \stdClass();
        $object->foo = 0;
        $object->bar = 42;
        $object->pass = 'always';

        $validator = new ValidatorBuilder();
        $validator->add(
            'foo',
            Validator::numeric()
        );
        $validator->add(
            'bar',
            Validator::required()
        );
        $validator->add(
            'bar',
            Validator::callback('is_int')
        );
        $validator->add(
            'quz',
            Validator::numeric()
        );
        self::assertTrue($validator->validate($object));
        self::assertTrue($validator->assert($object));
    }

    /**
     * Setup multi builder for empty object
     * @expectedException \Bluz\Validator\Exception\ValidatorException
     */
    public function testValidatorBuilderForEmptySet()
    {
        $validator = new ValidatorBuilder();
        $validator->add(
            'foo',
            Validator::required()
        );
        $validator->add(
            'bar',
            Validator::numeric()
        );
        self::assertFalse($validator->validate([]));
        self::assertFalse($validator->assert([]));
    }
}
