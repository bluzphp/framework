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
 * Concrete realization of Table class without row class specified.
 *
 * @category Tests
 * @package  Bluz\Db
 *
 * @author   Dmitriy Savchenko <login.was.here@gmail.com>
 */
class ConcreteTableWithoutRowClass extends Table
{
    protected $name = 'foo';
    protected $primary = ['bar', 'baz'];
}
