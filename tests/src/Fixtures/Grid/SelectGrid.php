<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/skeleton
 */

/**
 * @namespace
 */

namespace Bluz\Tests\Fixtures\Grid;

use Bluz\Db\Query\Select;
use Bluz\Grid\Grid;
use Bluz\Grid\Source\SelectSource;

/**
 * Test Grid based on SQL
 *
 * @category Application
 * @package  Test
 */
class SelectGrid extends Grid
{
    /**
     * @var string
     */
    protected $uid = 'sql';

    /**
     * Init SelectSource
     *
     * @return void
     * @throws \Bluz\Grid\GridException
     */
    public function init(): void
    {
        // Array
        $adapter = new SelectSource();

        $select = new Select();
        $select->select('*')->from('test', 't');

        $adapter->setSource($select);

        $this->setAdapter($adapter);
        $this->setDefaultLimit(10);
        $this->setAllowOrders(['name', 'id', 'status']);
        $this->setAllowFilters(['status', 'id', 'email']);
    }
}
