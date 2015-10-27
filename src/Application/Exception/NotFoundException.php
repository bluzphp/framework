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
 * NotFound Exception
 *
 * @package  Bluz\Application\Exception
 * @author   Anton Shevchuk
 */
class NotFoundException extends ApplicationException
{
    /**
     * @var string exception message
     */
    protected $message = "Page Not Found";

    /**
     * @var integer HTTP Code for "Not Found"
     */
    protected $code = 404;
}
