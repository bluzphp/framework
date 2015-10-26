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
 * @author   Anton Shevchuk
 */
class UnauthorizedException extends ApplicationException
{
    /**
     * @var string exception message
     */
    protected $message = "Unauthorized";

    /**
     *
     * @var integer HTTP code for "Unauthorized"
     */
    protected $code = 401;
}
