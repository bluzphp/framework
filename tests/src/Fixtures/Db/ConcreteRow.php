<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Fixtures\Db;

use Bluz\Db\Row;

/**
 * Concrete realization of Table class.
 *
 * @category Tests
 * @package  Bluz\Db
 *
 * @author Eugene Zabolotniy <realbaziak@gmail.com>
 */
class ConcreteRow extends Row
{
    protected $tableClass = 'Bluz\Tests\Fixtures\Db\ConcreteTable';
}
