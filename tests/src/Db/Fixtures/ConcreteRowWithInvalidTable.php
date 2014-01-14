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
 * Concrete realization of Table class with invalid table specified.
 *
 * @category Tests
 * @package  Bluz\Db
 * 
 * @author Dmitriy Savchenko <login.was.here@gmail.com>
 */
class ConcreteRowWithInvalidTable extends Bluz\Db\Row
{
    protected $table = 'InvalidTable';
}
