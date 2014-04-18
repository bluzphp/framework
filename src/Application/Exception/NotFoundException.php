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
class NotFoundException extends ApplicationException
{
    /**
     * Exception message
     * @var string
     */
    protected $message = "Page Not Found";

    /**
     * Not Found HTTP code
     * @var int
     */
    protected $code = 404;
}
