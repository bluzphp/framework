<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Validator;

use Bluz\Validator\Exception\ValidatorFormException;

/**
 * Validator Builder
 *
 * @package  Bluz\Validator
 * @author   Anton Shevchuk
 */
class ValidatorForm implements ValidatorInterface
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
     * Exception with list of validation errors
     *
     *   ['foo'] => "some field error"
     *   ['bar'] => "some field error"
     *
     * @var ValidatorFormException
     */
    protected $exception;

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
        $this->exception = new ValidatorFormException();

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
            $this->exception->setError($key, $this->validators[$key]->getError());
        }

        return $result;
    }

    /**
     * Assert
     *
     * @param  mixed $input
     *
     * @return void
     * @throws ValidatorFormException
     */
    public function assert($input)
    {
        if (!$this->validate($input)) {
            throw $this->exception;
        }
    }

    /**
     * @inheritdoc
     */
    public function __invoke($input) : bool
    {
        return $this->validate($input);
    }

    /**
     * Get errors
     *
     * @return array
     */
    public function getErrors() : array
    {
        return $this->exception->getErrors();
    }

    /**
     * Has errors?
     *
     * @return bool
     */
    public function hasErrors() : bool
    {
        return $this->exception->hasErrors();
    }
}
