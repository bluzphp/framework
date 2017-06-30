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
 * Table Not Found Exception
 *
 * @package  Bluz\Db\Exception
 * @author   Eugene Zabolotniy <realbaziak@gmail.com>
 */
class TableNotFoundException extends DbException
{
    /**
     * Exception message
     *
     * @var string
     */
    public $message = 'Table not found';
}
