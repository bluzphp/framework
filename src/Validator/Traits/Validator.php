<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Validator\Traits;

use Bluz\Validator\Exception\ValidatorException;
use Bluz\Validator\ValidatorChain;
use Bluz\Validator\ValidatorForm;

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
     * @var ValidatorForm instance
     */
    private $validatorForm;

    /**
     * Get ValidatorBuilder
     *
     * @return ValidatorForm
     */
    private function getValidatorForm(): ValidatorForm
    {
        if (!$this->validatorForm) {
            $this->validatorForm = new ValidatorForm();
        }
        return $this->validatorForm;
    }

    /**
     * Add ValidatorChain
     *
     * @param string $name
     *
     * @return ValidatorChain
     */
    public function addValidator(string $name): ValidatorChain
    {
        return $this->getValidatorForm()->add($name);
    }

    /**
     * Get ValidatorChain
     *
     * @param string $name
     *
     * @return ValidatorChain
     */
    public function getValidator(string $name): ValidatorChain
    {
        return $this->getValidatorForm()->get($name);
    }

    /**
     * Validate input data
     *
     * @param  array $input
     *
     * @return bool
     */
    public function validate($input): bool
    {
        return $this->getValidatorForm()->validate($input);
    }

    /**
     * Assert input data
     *
     * @param  array $input
     *
     * @throws ValidatorException
     */
    public function assert($input): void
    {
        $this->getValidatorForm()->assert($input);
    }
}
