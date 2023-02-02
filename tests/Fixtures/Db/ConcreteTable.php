<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Tests\Fixtures\Db;

use Bluz\Db\Table;
use Bluz\Db\Traits\TableRelations;

/**
 * Concrete realization of Table class.
 *
 * @category Tests
 * @package  Bluz\Db
 *
 * @author   Eugene Zabolotniy <realbaziak@gmail.com>
 */
class ConcreteTable extends Table
{
    use TableRelations;

    protected $name = 'foo';
    protected $primary = ['bar', 'baz'];
    protected $rowClass = 'ConcreteRow';
}
