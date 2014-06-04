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
use Bluz\Validator\Validator;
use Bluz\Validator\ValidatorBuilder;

/**
 * ValidatorBuilderTest
 *
 * @package  Bluz\Tests
 *
 * @author   Anton Shevchuk
 * @created  04.06.2014 13:34
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
        } catch (\Exception $e) {
            $this->assertEquals('Invalid Arguments', $e->getMessage());
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
                Validator::callback('is_int')
            );
            $validator->add(
                'bar',
                Validator::notEmpty(),
                Validator::callback('is_int')
            );
            $validator->assert(['foo' => 'something']);
        } catch (\Exception $e) {

            $this->assertEquals('Invalid Arguments', $e->getMessage());

            $errors = $validator->getErrors();
            $this->assertArrayHasKey('foo', $errors);
            $this->assertArrayHasKey('bar', $errors);
        }
    }
}
