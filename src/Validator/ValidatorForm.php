<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Validator;

use Bluz\Validator\Exception\ValidatorException;

/**
 * Validator Builder
 *
 * @package  Bluz\Validator
 * @author   Anton Shevchuk
 */
class ValidatorForm
{
    /**
     * Stack of validators
     *
     *   ['foo'] => ValidatorChain
     *   ['bar'] => ValidatorChain
     *
     * @var ValidatorChain[]
     */
    protected $validators = [];

    /**
     * list of validation errors
     *
     *   ['foo'] => "some field error"
     *   ['bar'] => "some field error"
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Add chain to form
     *
     * @param string      $name
     *
     * @return ValidatorChain
     */
    public function add($name) : ValidatorChain
    {
        $this->validators[$name] = $this->validators[$name] ?? Validator::create();
        return $this->validators[$name];
    }

    /**
     * Validate chain of rules
     *
     * @param  array $input
     *
     * @return bool
     */
    public function validate($input) : bool
    {
        $this->resetErrors();

        // run chains
        foreach ($this->validators as $key => $validators) {
            $this->validateItem($key, $input[$key] ?? null);
        }

        return !$this->hasErrors();
    }

    /**
     * Validate chain of rules for single item
     *
     * @param  string $key
     * @param  mixed  $value
     *
     * @return bool
     */
    protected function validateItem($key, $value): bool
    {
        // run validators chain
        $result = $this->validators[$key]->validate($value);

        if (!$result) {
            $this->setError($key, $this->validators[$key]->getError());
        }

        return $result;
    }

    /**
     * Assert
     *
     * @param  mixed $input
     *
     * @return bool
     * @throws ValidatorException
     */
    public function assert($input)
    {
        if (!$this->validate($input)) {
            $exception = new ValidatorException();
            $exception->setErrors($this->getErrors());
            throw $exception;
        }
        return true;
    }

    /**
     * Add Error by field name
     *
     * @param  string $name
     * @param  string $message
     *
     * @return void
     */
    protected function setError($name, $message)
    {
        $this->errors[$name] = $message;
    }

    /**
     * Reset errors
     *
     * @return void
     */
    protected function resetErrors()
    {
        $this->errors = [];
    }

    /**
     * Get errors
     *
     * @return array
     */
    public function getErrors() : array
    {
        return $this->errors;
    }

    /**
     * Has errors?
     *
     * @return bool
     */
    public function hasErrors() : bool
    {
        return (bool)count($this->errors);
    }
}
