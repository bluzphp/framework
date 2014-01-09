<?php
/**
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
 * @category Application
 * @package  Exception
 *
 * @author   Anton Shevchuk
 * @created  23.01.13 17:46
 */
class NotFoundException extends ApplicationException
{
    /**
     * @var string
     */
    protected $message = "Page Not Found";

    /**
     * Redirect HTTP code
     * 301 Moved Permanently
     * 302 Found
     * @var int
     */
    protected $code = 404;
}
