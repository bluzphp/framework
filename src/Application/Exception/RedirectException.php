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
 * Redirect Exception
 *
 * @package  Bluz\Application\Exception
 *
 * @author   Anton Shevchuk
 * @created  23.01.13 17:40
 */
class RedirectException extends ApplicationException
{
    /**
     * Exception message consist Location data
     * @var string
     */
    protected $message = "Application Redirect";

    /**
     * Redirect HTTP code
     *
     * - 301 Moved Permanently
     * - 302 Moved Temporarily
     * - 307 Temporary Redirect
     *
     * @var int
     */
    protected $code = 302;
}
