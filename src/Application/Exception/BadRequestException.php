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
 * BadRequest Exception
 *
 * @package  Bluz\Application\Exception
 * @author   Anton Shevchuk
 */
class BadRequestException extends ApplicationException
{
    /**
     * @var string exception message
     */
    protected $message = "Bad Request";

    /**
     * @var integer HTTP Code for "Bad Request"
     */
    protected $code = 400;
}
