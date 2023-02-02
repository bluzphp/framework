<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/skeleton
 */

/**
 * @namespace
 */

namespace Bluz\Tests\Fixtures\Models\Test;

use Bluz\Http\StatusCode;
use Bluz\Proxy\Db;
use Bluz\Proxy\Response;

/**
 * Crud based on Db\Table
 *
 * @package  Bluz\Tests\Fixtures
 *
 * @author   Anton Shevchuk
 * @created  03.09.12 13:11
 */
class Crud extends \Bluz\Crud\Table
{
    /**
     * Return table instance for manipulation
     *
     * @return \Bluz\Db\TableInterface
     */
    public function getTable()
    {
        if (!$this->table) {
            /**
             * @var Table $tableClass
             */
            $table = Table::getInstance();

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
     *
     * @return array|int|mixed
     */
    public function readSet(int $offset = 0, int $limit = 10, array $params = [])
    {
        $select = Db::select('*')
            ->from('test', 't');

        if ($limit) {
            $selectPart = $select->getQueryPart('select');
            $selectPart = 'SQL_CALC_FOUND_ROWS ' . current($selectPart);
            $select->select($selectPart);

            $select->setLimit($limit);
            $select->setOffset($offset);
        }

        $result = $select->execute(Row::class);

        if ($limit) {
            $total = (int)Db::fetchOne('SELECT FOUND_ROWS()');
        } else {
            $total = count($result);
        }

        if (count($result) < $total) {
            Response::setStatusCode(StatusCode::PARTIAL_CONTENT);
            Response::setHeader(
                'Content-Range',
                'items ' . $offset . '-' . ($offset + count($result)) . '/' . $total
            );
        }

        return [$result, $total];
    }
}
