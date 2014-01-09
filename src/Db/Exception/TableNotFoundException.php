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
 * TableNotFoundException class.
 *
 * @category Bluz
 * @package  Db
 *
 * @author   Eugene Zabolotniy <realbaziak@gmail.com>
 * @since    1.0
 */
class TableNotFoundException extends DbException
{
    public $message = 'Relation not found';
}
