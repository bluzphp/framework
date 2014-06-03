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
     *   ['foo'] => [Validator, Validator,...]
     *   ['bar'] => [Validator, Validator,...]
     *
     * @var array
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
     * @param Validator $validator...
     * @return ValidatorBuilder
     */
    public function add($name, $validator)
    {
        $validators = func_get_args();
        array_shift($validators);

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
     * @param array|object $input
     * @return bool
     */
    public function validate($input)
    {
        $result = true;
        foreach ($input as $key => $value) {
            if (!isset($this->validators[$key])) {
                continue;
            }
            foreach ($this->validators[$key] as $validator) {
                /* @var Validator $validator */
                if (!$validator->getName()) {
                    // setup field name as property name
                    $validator->setName(ucfirst($key));
                }

                if (!$validator->validateProperty($input, $key)) {
                    if (!isset($this->errors[$key])) {
                        $this->errors[$key] = array();
                    }
                    $this->errors[$key][] = $validator->getError();
                    $result = false;
                    break; // stop on first fail
                }
            }
        }
        return $result;
    }

    /**
     * Assert
     *
     * @param array|object $input
     * @return void
     */
    public function assert($input)
    {
        if (!$this->validate($input)) {
            throw new ValidatorException("Invalid Arguments");
        }
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
 