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
 * Not Acceptable Exception
 *
 * @package  Bluz\Application\Exception
 * @author   Anton Shevchuk
 */
class NotAcceptableException extends ApplicationException
{
    /**
     * @var string exception message
     */
    protected $message = "Not Acceptable";

    /**
     * @var integer HTTP Code for "Not Acceptable"
     */
    protected $code = 406;
}
