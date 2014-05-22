<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/skeleton
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Fixtures\Models;

/**
 * Test Row
 *
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $status enum('active','disable','delete')
 *
 * @package  Bluz\Tests\Fixtures
 */
class TestRow extends \Bluz\Db\Row
{
    /**
     * Return table instance for manipulation
     *
     * @return TestTable
     */
    public function getTable()
    {
        return TestTable::getInstance();
    }
}
