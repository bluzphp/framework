<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Db\Exception;

/**
 * Invalid Primary Key Exception
 *
 * @package  Bluz\Db\Exception
 * @author   Eugene Zabolotniy
 */
class InvalidPrimaryKeyException extends DbException
{
    /**
     * Exception message
     *
     * @var string
     */
    public $message = "Wrong primary key";
}
