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
 * Forbidden Exception
 *
 * @package  Bluz\Application\Exception
 * @author   Anton Shevchuk
 */
class ForbiddenException extends ApplicationException
{
    /**
     * @var string exception message
     */
    protected $message = "Forbidden";

    /**
     * @var integer HTTP Code for "Forbidden"
     */
    protected $code = 403;
}
