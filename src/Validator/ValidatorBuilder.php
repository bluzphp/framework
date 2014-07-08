<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Validator;

use Bluz\Validator\Exception\ValidatorException;

/**
 * Validator Builder
 *
 * @package  Bluz\Validator
 *
 * @author   Anton Shevchuk
 * @created  03.06.2014 10:58
 */
class ValidatorBuilder
{
    /**
     * Stack of validators
     *
     *   ['foo'] => Validator
     *   ['bar'] => Validator
     *
     * @var Validator[]
     */
    protected $validators = array();

    /**
     * @var array
     */
    protected $errors = array();

    /**
     * add
     *
     * @param string $name
     * @internal Validator $validator,...
     * @return ValidatorBuilder
     */
    public function add($name)
    {
        $validators = func_get_args();

        // extract name, not used
        array_shift($validators);

        // extract validator
        $validator = array_shift($validators);

        // merge validator chains inside one validator
        if (sizeof($validators)) {
            $validator = $this->mergeValidators($validator, $validators);
        }

        // if already exists, merge it
        if (isset($this->validators[$name])) {
            $this->validators[$name] = $this->mergeValidators($this->validators[$name], $validator);
        } else {
            $this->validators[$name] = $validator;
        }
        return $this;
    }

    /**
     * mergeValidators
     *
     * @param Validator $recipient
     * @param Validator[] $validators
     * @return Validator
     */
    protected function mergeValidators($recipient, $validators)
    {
        foreach ($validators as $validator) {
            $rules = $validator->getRules();
            $recipient->addRules($rules);
        }
        return $recipient;
    }

    /**
     * Validate chain of rules
     *
     * @param array|object $input
     * @return bool
     */
    public function validate($input)
    {
        $this->errors = array();
        $result = true;
        // check be validators
        foreach ($this->validators as $key => $validators) {
            if (!$this->validateItem($key, $input)) {
                $result = false;
            }
        }
        return $result;
    }

    /**
     * Validate chain of rules for single item
     *
     * @param string $key
     * @param array|object $input
     * @return bool
     */
    public function validateItem($key, $input)
    {
        // w/out any rules element is valid
        if (!isset($this->validators[$key])) {
            return true;
        }

        /* @var Validator $validator */
        $validator = $this->validators[$key];

        // run validators chain
        if (!$validator->getName()) {
            // setup field name as property name
            $validator->setName(ucfirst($key));
        }

        // check be validators
        // extract input from ...
        if (is_array($input) && isset($input[$key])) {
            // array
            $value = $input[$key];
        } elseif (is_object($input) && isset($input->{$key})) {
            // object
            $value = $input->{$key};
        } else {
            // ... oh, not exists key
            // check chains for required
            if (!$validator->isRequired()) {
                return true;
            }

            $value = false;
        }

        if (!$validator->validate($value)) {
            $this->errors[$key] = $validator->getError();
            return false;
        }
        return true;
    }

    /**
     * Assert
     *
     * @param mixed $input
     * @return bool
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
     * Get errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
