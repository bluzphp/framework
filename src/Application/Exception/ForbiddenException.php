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
 * Forbidden Exception
 *
 * @package  Bluz\Application\Exception
 * @author   Anton Shevchuk
 */
class ForbiddenException extends ApplicationException
{
    /**
     * @var integer HTTP Code
     */
    protected $code = StatusCode::FORBIDDEN;
}
