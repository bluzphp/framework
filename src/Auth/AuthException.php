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
namespace Bluz\Auth;

use Bluz\Common\Exception\CommonException;

/**
 * Exception
 *
 * @package Bluz\Auth
 *
 * @author   Anton Shevchuk
 * @created  12.07.11 16:46
 */
class AuthException extends CommonException
{
    /**
     * HTTP code
     * 401 Unauthorized
     * 403 Forbidden
     * @var int
     */
    protected $code = 401;
}
