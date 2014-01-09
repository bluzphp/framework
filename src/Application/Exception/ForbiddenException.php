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
 * @created  13.08.13 14:01
 */
class ForbiddenException extends ApplicationException
{
    /**
     * @var string
     */
    protected $message = "Forbidden";

    /**
     * Forbidden HTTP code
     * @var int
     */
    protected $code = 403;
}
