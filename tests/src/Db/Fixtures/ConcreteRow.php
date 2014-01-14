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
class ConcreteRow extends Bluz\Db\Row
{
    protected $table = 'Bluz\Tests\Db\Fixtures\ConcreteTable';
}
