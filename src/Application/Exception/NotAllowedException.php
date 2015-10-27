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
 * Not Allowed Exception
 *
 * @package  Bluz\Application\Exception
 * @author   Anton Shevchuk
 */
class NotAllowedException extends ApplicationException
{
    /**
     * @var string exception message
     */
    protected $message = "Method Not Allowed";

    /**
     * @var integer HTTP Code for "Method Not Allowed"
     */
    protected $code = 405;
}
