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
 * @created  23.01.13 17:40
 */
class RedirectException extends ApplicationException
{
    /**
     * @var string
     */
    protected $message = "Application Redirect";

    /**
     * Redirect HTTP code
     * 301 Moved Permanently
     * 302 Found
     * @var int
     */
    protected $code = 302;
}
