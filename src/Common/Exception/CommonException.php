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
namespace Bluz\Common\Exception;

/**
 * Basic Exception for Bluz framework
 *
 * @package  Bluz\Common\Exception
 * @author   Anton Shevchuk
 * @created  06.07.11 16:46
 */
class CommonException extends \Exception
{
    /**
     * @var int Used as default HTTP code for exceptions
     */
    protected $code = 500;
}
