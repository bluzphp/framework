<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Validator\Traits;

use Bluz\Validator\ValidatorBuilder;

/**
 * Validator trait
 *
 * Example of usage
 * <code>
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
 * </code>
 *
 * @package  Bluz\Validator\Traits
 * @author   Anton Shevchuk
 */
trait Validator
{
    /**
     * @var ValidatorBuilder instance of ValidatorBuilder
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
     * @param  string $name
     * @param  \Bluz\Validator\Validator[] $validators
     * @return Validator
     */
    protected function addValidator($name, ...$validators)
    {
        $this->getValidatorBuilder()->add($name, ...$validators);
        return $this;
    }

    /**
     * Validate input data
     *
     * @param  array $input
     * @return bool
     */
    public function validate($input) : bool
    {
        return $this->getValidatorBuilder()->validate($input);
    }

    /**
     * Assert input data
     *
     * @param  array $input
     * @return bool
     */
    public function assert($input)
    {
        return $this->getValidatorBuilder()->assert($input);
    }
}
