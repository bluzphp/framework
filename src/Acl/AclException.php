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
namespace Bluz\Acl;

use Bluz\Common\Exception;

/**
 * Exception
 *
 * @package  Bluz\Acl
 *
 * @author   Anton Shevchuk
 * @created  06.03.12 15:53
 */
class AclException extends Exception
{
    /**
     * HTTP code
     * 401 Unauthorized
     * 403 Forbidden
     * @var int
     */
    protected $code = 401;
}
