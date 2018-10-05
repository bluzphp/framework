<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Validator\Exception;

use Bluz\Http\Exception\BadRequestException;

/**
 * Validator Exception
 *
 * @package  Bluz\Validator\Exception
 * @author   Anton Shevchuk
 */
class ValidatorException extends BadRequestException
{
    /**
     * @var string exception message
     */
    protected $message = 'Invalid Arguments';

    /**
     * @var array of error's messages
     */
    protected $errors = [];

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
    public function setErrors(array $errors): void
    {
        $this->errors = $errors;
    }

    /**
     * Add Error by field name
     *
     * @param  string $name
     * @param  string $message
     *
     * @return void
     */
    public function setError($name, $message): void
    {
        $this->errors[$name] = $message;
    }

    /**
     * Has errors?
     *
     * @return bool
     */
    public function hasErrors(): bool
    {
        return (bool) \count($this->errors);
    }
}
