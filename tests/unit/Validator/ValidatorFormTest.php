<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Validator;

use Bluz\Tests;
use Bluz\Validator\Exception\ValidatorFormException;
use Bluz\Validator\ValidatorForm;

/**
 * Class ValidatorBuilderTest
 *
 * @package Bluz\Tests\Validator
 */
class ValidatorFormTest extends Tests\FrameworkTestCase
{
    public function testSetCustomDescriptionOfValidatorChainShouldBeInValidatorFormException()
    {
        $validator = new ValidatorForm();
        try {
            $validator->add('some')
                ->callback('is_int')
                ->setDescription('is not numeric');
            $validator->assert(['some' => 'something']);
        } catch (ValidatorFormException $e) {
            self::assertEquals('Invalid Arguments', $e->getMessage());
            self::assertArrayHasKey('some', $e->getErrors());
            self::assertEquals('is not numeric', $e->getErrors()['some']);
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
        } catch (ValidatorFormException $e) {
            self::assertEquals('Invalid Arguments', $e->getMessage());

            $errors = $validator->getErrors();

            self::assertEqualsArray($errors, $e->getErrors());
            self::assertArrayHasKey('bar', $errors);
            self::assertArrayHasKey('quz', $errors);
        }
    }

    /**
     * @expectedException \Bluz\Validator\Exception\ValidatorException
     */
    public function testAssertEmptyDataShouldRaiseException()
    {
        $validator = new ValidatorForm();
        $validator->add('foo')->required();
        $validator->add('bar')->numeric();
        $validator->assert([]);
    }
}
