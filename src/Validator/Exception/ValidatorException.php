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
namespace Bluz\Validator\Exception;

/**
 * Validator Exception
 *
 * @package  Bluz\Validator\Exception
 *
 * @author   Anton Shevchuk
 * @created  30.05.2014 14:47
 */
class ValidatorException extends \InvalidArgumentException
{
    /**
     * Bad Request HTTP Code
     * @var int
     */
    protected $code = 400;

    /**
     * @var array of error messages
     */
    protected $errors = array();

    /**
     * Set errors
     *
     * @param array $errors
     * @return array
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;
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
