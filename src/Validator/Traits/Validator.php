<?php
/**
 * @namespace
 */
namespace Bluz\Validator\Traits;

use Bluz\Validator\ValidatorBuilder;

/**
 * Row
 *
 * @package  Bluz\Validator\Traits
 *
 * @method   array toArray()
 *
 * @author   Anton Shevchuk
 * @created  04.07.2014 10:14
 */
trait Validator
{
    /**
     * @var ValidatorBuilder
     */
    protected $validatorBuilder;

    /**
     * Get ValidatorBuilder
     *
     * @return ValidatorBuilder
     */
    protected function getValidatorBuilder()
    {
        if (!$this->validatorBuilder) {
            $this->validatorBuilder = new ValidatorBuilder();
        }
        return $this->validatorBuilder;
    }

    /**
     * Add Validator for field
     *
     * @internal string $name
     * @internal Validator $validator,...
     * @return self
     */
    protected function addValidator()
    {
        call_user_func_array([$this->getValidatorBuilder(), 'add'], func_get_args());
        return $this;
    }

    /**
     * Validate input data
     *
     * @param array|object $input
     * @return boolean
     */
    public function validate($input)
    {
        return $this->getValidatorBuilder()->validate($input);
    }

    /**
     * Assert input data
     *
     * @param array|object $input
     * @return boolean
     */
    public function assert($input)
    {
        return $this->getValidatorBuilder()->assert($input);
    }
}
