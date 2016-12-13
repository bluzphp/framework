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
 * Unauthorized Exception
 *
 * @package  Bluz\Application\Exception
 * @author   Anton Shevchuk
 */
class UnauthorizedException extends ApplicationException
{
    /**
     * @var integer HTTP code
     */
    protected $code = StatusCode::UNAUTHORIZED;
}
