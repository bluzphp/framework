<?php
/**
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
 * @category Bluz
 * @package  Db
 *
 * @author   Eugene Zabolotniy
 */
class InvalidPrimaryKeyException extends DbException
{
    public $message = "Wrong primary key";
}
