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
 * Wrong realization of Table class.
 *
 * @category Tests
 * @package  Bluz\Db
 * 
 * @author Eugene Zabolotniy <realbaziak@gmail.com>
 */
class WrongKeysTable extends Bluz\Db\Table
{
    protected $table = 'foo';
}
