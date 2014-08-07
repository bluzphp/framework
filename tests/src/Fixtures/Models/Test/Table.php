<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/skeleton
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Fixtures\Models\Test;

/**
 * Table
 *
 * @package  Bluz\Tests\Fixtures
 *
 * @method   static Row findRow($primaryKey)
 * @method   static Row findRowWhere($whereList)
 *
 * @author   Anton Shevchuk
 * @created  08.07.11 17:36
 */
class Table extends \Bluz\Db\Table
{
    /**
     * Table
     *
     * @var string
     */
    protected $table = 'test';

    /**
     * Primary key(s)
     * @var array
     */
    protected $primary = array('id');

    /**
     * Class name
     * @var string
     */
    protected $rowClass = '\Bluz\Tests\Fixtures\Models\Test\Row';
}
