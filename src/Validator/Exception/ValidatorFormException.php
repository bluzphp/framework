<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Validator\Exception;

/**
 * Validator Exception
 *
 * @package  Bluz\Validator\Exception
 * @author   Anton Shevchuk
 */
class ValidatorFormException extends ValidatorException
{
    /**
     * @var array of error's messages
     */
    protected $errors;

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param array $errors
     */
    public function setErrors(array $errors)
    {
        $this->errors = $errors;
    }
}
