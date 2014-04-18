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
namespace Bluz\Db\Exception;

/**
 * WrongPrimaryKeyException
 *
 * @package  Bluz\Db\Exception
 * @author   Eugene Zabolotniy
 */
class InvalidPrimaryKeyException extends DbException
{
    /**
     * Exception message
     * @var string
     */
    public $message = "Wrong primary key";
}
