<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Validator;

use Bluz\Http\StatusCode;
use Bluz\Tests;
use Bluz\Validator\Exception\ValidatorException;
use Bluz\Validator\ValidatorForm;

/**
 * Class ValidatorBuilderTest
 *
 * @package Bluz\Tests\Validator
 */
class ValidatorFormTest extends Tests\FrameworkTestCase
{
    public function testSetCustomDescriptionOfValidatorChainShouldBeInValidatorException()
    {
        $validator = new ValidatorForm();
        try {
            $validator->add('some')
                ->callback('is_int')
                ->setDescription('is not numeric');
            $validator->assert(['some' => 'something']);
        } catch (ValidatorException $e) {
            self::assertEquals(StatusCode::BAD_REQUEST->message(), $e->getMessage());
            self::assertArrayHasKey('some', $e->getErrors());
            self::assertEquals('is not numeric', $e->getErrors()['some']);
        }
    }

    public function testAssertOptionalAndRequiredFields()
    {
        $validator = new ValidatorForm();
        try {
            $validator->add('foo')->callback('is_int');
            $validator->add('bar')->required()->callback('is_int');
            $validator->assert([]);
        } catch (ValidatorException $e) {
            self::assertEquals(StatusCode::BAD_REQUEST->message(), $e->getMessage());

            $errors = $validator->getErrors();

            self::assertEqualsArray($errors, $e->getErrors());
            self::assertArrayNotHasKey('foo', $errors);
            self::assertArrayHasKey('bar', $errors);
        }
    }

    public function testAssertInvalidDataShouldRaiseExceptionWithErrorsMessages()
    {
        $validator = new ValidatorForm();
        try {
            $validator->add('foo')->required()->callback('is_int');
            $validator->add('bar')->required()->callback('is_int');
            $validator->add('quz')->required()->callback('is_int');
            $validator->assert(['foo' => 42, 'bar' => 'something']);
        } catch (ValidatorException $e) {
            self::assertEquals(StatusCode::BAD_REQUEST->message(), $e->getMessage());

            $errors = $validator->getErrors();
            self::assertEqualsArray($errors, $e->getErrors());
            self::assertArrayNotHasKey('foo', $errors);
            self::assertArrayHasKey('bar', $errors);
            self::assertArrayHasKey('quz', $errors);
        }
    }

    public function testAssertEmptyDataShouldRaiseException()
    {
        $this->expectException(ValidatorException::class);
        $validator = new ValidatorForm();
        $validator->add('foo')->required();
        $validator->add('bar')->numeric();
        $validator->assert([]);
    }
}
