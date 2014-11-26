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
 * Not Acceptable Exception
 *
 * @package  Bluz\Application\Exception
 *
 * @author   Anton Shevchuk
 * @created  25.11.14 17:00
 */
class NotAcceptableException extends ApplicationException
{
    /**
     * Exception message
     * @var string
     */
    protected $message = "Not Acceptable";

    /**
     * Method Not Allowed
     * @var int
     */
    protected $code = 406;
}
