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
 * Not Allowed Exception
 *
 * @package  Bluz\Application\Exception
 *
 * @author   Anton Shevchuk
 * @created  25.11.14 16:56
 */
class NotAllowedException extends ApplicationException
{
    /**
     * Exception message
     * @var string
     */
    protected $message = "Method Not Allowed";

    /**
     * Method Not Allowed
     * @var int
     */
    protected $code = 405;
}
