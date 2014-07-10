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
 * Crud based on Db\Table
 *
 * @package  Bluz\Tests\Fixtures
 *
 * @author   Anton Shevchuk
 * @created  03.09.12 13:11
 */
class TestCrud extends \Bluz\Crud\Table
{
    /**
     * Return table instance for manipulation
     *
     * @return TestTable
     */
    public function getTable()
    {
        if (!$this->table) {
            /**
             * @var TestTable $tableClass
             */
            $table = TestTable::getInstance();

            $this->setTable($table);
        }
        return $this->table;
    }

    /**
     * {@inheritdoc}
     *
     * @param int $offset
     * @param int $limit
     * @param array $params
     * @return array|int|mixed
     */
    public function readSet($offset = 0, $limit = 10, $params = array())
    {
        $select = app()->getDb()
            ->select('*')
            ->from('test', 't');

        if ($limit) {
            $selectPart = $select->getQueryPart('select');
            $selectPart[0] = 'SQL_CALC_FOUND_ROWS ' . current($selectPart);
            $select->select($selectPart);

            $select->setLimit($limit);
            $select->setOffset($offset);
        }

        $result = $select->execute('\\Bluz\\Tests\\Fixtures\\Models\\TestRow');

        if ($limit) {
            $total = app()->getDb()->fetchOne('SELECT FOUND_ROWS()');
        } else {
            $total = sizeof($result);
        }

        if (sizeof($result) < $total) {
            app()->getResponse()
                ->setCode(206)
                ->setHeader('Content-Range', 'items '.$offset.'-'.($offset+sizeof($result)).'/'. $total);
        }

        return $result;
    }
}
