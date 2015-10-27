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
 * NotImplemented Exception
 *
 * @package  Bluz\Application\Exception
 * @author   Anton Shevchuk
 */
class NotImplementedException extends ApplicationException
{
    /**
     * @var string exception message
     */
    protected $message = "Not Implemented";

    /**
     * @var integer HTTP code for "Not Implemented"
     */
    protected $code = 501;
}
