<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/skeleton
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Crud\Fixtures;

/**
 * Crud based on Db\Table
 *
 * @package  Bluz\Tests\Fixtures
 *
 * @author   Anton Shevchuk
 * @created  03.09.12 13:11
 */
class EmptyCrud extends \Bluz\Crud\AbstractCrud
{
    /**
     * @return array
     */
    public function getPrimaryKey()
    {
        return array();
    }
}
