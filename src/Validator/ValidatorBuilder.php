<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
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
class ValidatorBuilder
{
    /**
     * Stack of validators
     *
     *   ['foo'] => [Validator, ...]
     *   ['bar'] => [Validator, ...]
     *
     * @var array
     */
    protected $validators = [];

    /**
     * list of validation errors
     *
     *   ['foo'] => ["error one", ...]
     *   ['bar'] => ["error one", ...]
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Add validator to builder
     * @param string $name
     * @param Validator[] ...$validators
     * @return ValidatorBuilder
     */
    public function add($name, ...$validators)
    {
        if (isset($this->validators[$name])) {
            $this->validators[$name] = array_merge($this->validators[$name], $validators);
        } else {
            $this->validators[$name] = $validators;
        }
        return $this;
    }

    /**
     * Validate chain of rules
     *
     * @param  array $input
     * @return bool
     */
    public function validate($input) : bool
    {
        $this->resetErrors();

        foreach ($this->validators as $key => $validators) {
            $this->validateItem($key, $input[$key] ?? null);
        }

        return !$this->hasErrors();
    }

    /**
     * Validate chain of rules for single item
     *
     * @param  string $key
     * @param  mixed $value
     * @return bool
     */
    public function validateItem($key, $value) : bool
    {
        $validators = $this->validators[$key] ?? null;

        // w/out any rules element is valid
        if (is_null($validators)) {
            return true;
        }

        // w/out value
        if (is_null($value)) {
            // should check is required or not
            $required = false;
            foreach ($validators as $validator) {
                /* @var Validator $validator */
                if ($validator->isRequired()) {
                    $required = true;
                    break;
                }
            }

            if ($required) {
                // required
                $value = '';
            } else {
                // not required
                return true;
            }
        }

        // run validators chain
        foreach ($validators as $validator) {
            /* @var Validator $validator */
            if (!$validator->getName()) {
                // setup field name as property name
                $validator->setName(ucfirst($key));
            }

            if (!$validator->validate($value)) {
                $this->addError($key, $validator->getError());
                return false;
            }
        }
        return true;
    }

    /**
     * Assert
     *
     * @param  mixed $input
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
     * @return void
     */
    protected function addError($name, $message)
    {
        if (isset($this->errors[$name])) {
            $this->errors[$name][] = $message;
        } else {
            $this->errors[$name] = [$message];
        }
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
     * Reset errors
     *
     * @return void
     */
    public function resetErrors()
    {
        $this->errors = [];
    }

    /**
     * Has errors?
     *
     * @return bool
     */
    public function hasErrors() : bool
    {
        return (bool) count($this->errors);
    }
}
