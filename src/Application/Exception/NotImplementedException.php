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
 * Exception
 *
 * @package  Bluz\Application\Exception
 *
 * @author   Anton Shevchuk
 * @created  13.08.13 13:09
 */
class NotImplementedException extends ApplicationException
{
    /**
     * Exception message
     * @var string
     */
    protected $message = "Not Implemented";


    /**
     * Not Implemented HTTP Code
     * @var int
     */
    protected $code = 501;
}
