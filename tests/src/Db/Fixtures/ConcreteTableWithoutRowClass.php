<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Db\Fixtures;

use Bluz;
use Bluz\Db;

/**
 * Concrete realization of Table class without row class specified.
 *
 * @category Tests
 * @package  Bluz\Db
 *
 * @author Dmitriy Savchenko <login.was.here@gmail.com>
 */
class ConcreteTableWithoutRowClass extends Bluz\Db\Table
{
    protected $name = 'foo';
    protected $primary = ['bar', 'baz'];
}
