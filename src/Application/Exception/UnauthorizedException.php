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
 * Unauthorized Exception
 *
 * @package  Bluz\Application\Exception
 *
 * @author   Anton Shevchuk
 * @created  26.09.14 12:18
 */
class UnauthorizedException extends ApplicationException
{
    /**
     * Exception message
     * @var string
     */
    protected $message = "Unauthorized";

    /**
     * Unauthorized HTTP code
     * @var int
     */
    protected $code = 401;
}
