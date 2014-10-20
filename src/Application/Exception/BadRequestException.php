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
namespace Bluz\Application\Exception;

/**
 * BadRequest Exception
 *
 * @package  Bluz\Application\Exception
 *
 * @author   Anton Shevchuk
 * @created  23.01.13 17:46
 */
class BadRequestException extends ApplicationException
{
    /**
     * Exception message
     * @var string
     */
    protected $message = "Bad Request";

    /**
     * Bad Request HTTP Code
     * @var int
     */
    protected $code = 400;
}
