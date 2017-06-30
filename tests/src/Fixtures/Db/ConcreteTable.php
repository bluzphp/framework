<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */

namespace Bluz\Tests\Fixtures\Db;

use Bluz\Db\Table;

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
    protected $name = 'foo';
    protected $primary = ['bar', 'baz'];
    protected $rowClass = 'ConcreteRow';
}
