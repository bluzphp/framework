<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Tests\Fixtures\Db;

use Bluz\Db\Row;

/**
 * Concrete realization of Table class.
 *
 * @category Tests
 * @package  Bluz\Db
 *
 * @author   Eugene Zabolotniy <realbaziak@gmail.com>
 */
class ConcreteRow extends Row
{
    protected $tableClass = ConcreteTable::class;
}
