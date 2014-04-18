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
 * TableNotFoundException class.
 *
 * @package  Bluz\Db\Exception
 * @author   Eugene Zabolotniy <realbaziak@gmail.com>
 * @since    1.0
 */
class TableNotFoundException extends DbException
{
    /**
     * Exception message
     * @var string
     */
    public $message = 'Relation not found';
}
