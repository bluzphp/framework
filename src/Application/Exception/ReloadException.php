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

use Bluz\Http\StatusCode;

/**
 * Reload Exception
 *
 * @package  Bluz\Application\Exception
 * @author   Anton Shevchuk
 */
class ReloadException extends ApplicationException
{
    /**
     * @var string exception message
     */
    protected $message = "Application Reload";

    /**
     * @var integer HTTP code
     */
    protected $code = StatusCode::OK;
}
