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

use Bluz\Application\Exception\BadRequestException;

/**
 * Validator Exception
 *
 * @package  Bluz\Validator\Exception
 *
 * @author   Anton Shevchuk
 * @created  30.05.2014 14:47
 */
class ValidatorException extends BadRequestException
{
    /**
     * @var string Exception message
     */
    protected $message = "Invalid Arguments";

    /**
     * @var array Set of error messages
     */
    protected $errors = array();

    /**
     * Create and throw Exception
     * @param $key
     * @param $error
     * @return self
     */
    public static function exception($key, $error)
    {
        $exception = new self;
        $exception->setError($key, $error);
        return $exception;
    }

    /**
     * Set error by Key
     * @param string $key
     * @param string $error
     * @return void
     */
    public function setError($key, $error)
    {
        $this->errors[$key] = $error;
    }

    /**
     * Set errors
     * @param array $errors
     * @return void
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;
    }

    /**
     * Get errors
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
