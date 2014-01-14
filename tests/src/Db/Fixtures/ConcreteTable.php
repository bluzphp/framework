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
 * Concrete realization of Table class.
 *
 * @category Tests
 * @package  Bluz\Db
 * 
 * @author Eugene Zabolotniy <realbaziak@gmail.com>
 */
class ConcreteTable extends Bluz\Db\Table
{
    protected $table = 'foo';
    protected $primary = array('bar', 'baz');
    protected $rowClass = 'ConcreteRow';
}
