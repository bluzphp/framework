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
 * Exception
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
     * Redirect HTTP code
     *
     *  - 301 Moved Permanently
     *  - 302 Found
     * @var int
     */
    protected $code = 400;
}
