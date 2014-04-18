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

use Bluz\Common\Exception;

/**
 * Exception
 *
 * @package  Bluz\Application\Exception
 * @author   Anton Shevchuk
 * @created  17.12.12 16:46
 */
class ApplicationException extends Exception
{
    /**
     * Used as default HTTP code for exceptions
     * @var int
     */
    protected $code = 500;
}
