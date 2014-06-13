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
     * @internal param Validator $validator,...
     * @return ValidatorBuilder
     */
    public function add($name)
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
        // check be validators
        foreach ($this->validators as $key => $validators) {
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
                foreach ($validators as $validator) {
                    /* @var Validator $validator */
                    if ($validator->isRequired()) {
                        $this->errors[$key][] = $validator->getError();
                        $result = false;
                        break;
                    }
                }
                break;
            }

            // run validators chain
            foreach ($validators as $validator) {
                /* @var Validator $validator */
                if (!$validator->getName()) {
                    // setup field name as property name
                    $validator->setName(ucfirst($key));
                }

                if (!$validator->validate($value)) {
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
     * @param mixed $input
     * @return bool
     */
    public function assert($input)
    {
        if (!$this->validate($input)) {
            throw new ValidatorException("Invalid Arguments");
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
