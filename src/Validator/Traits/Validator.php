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
namespace Bluz\Validator\Traits;

use Bluz\Validator\ValidatorBuilder;

/**
 * Validator trait
 *
 * Example of usage
 *    use Bluz\Validator\Traits\Validator;
 *    use Bluz\Validator\Validator as v;
 *
 *    class Row extends Db\Row {
 *        use Validator;
 *        function beforeSave()
 *        {
 *             $this->addValidator(
 *                 'login',
 *                 v::required()->latin()->length(3, 255)
 *             );
 *        }
 *    }
 *
 * @package  Bluz\Validator\Traits
 *
 * @author   Anton Shevchuk
 * @created  04.07.2014 10:14
 */
trait Validator
{
    /**
     * @var ValidatorBuilder Instance of ValidatorBuilder
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
     * @param string $name
     * @param \Bluz\Validator\Validator $validator,...
     * @return self
     */
    protected function addValidator($name)
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
