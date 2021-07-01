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
 * Relation Not Found Exception
 *
 * @package  Bluz\Db\Exception
 * @author   Eugene Zabolotniy <realbaziak@gmail.com>
 */
class RelationNotFoundException extends DbException
{
    /**
     * Exception message
     *
     * @var string
     */
    public $message = 'Relation not found';
}
