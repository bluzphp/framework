<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Grid\Source;

use Bluz\Db;
use Bluz\Grid;
use Bluz\Grid\Data;
use Bluz\Grid\GridException;
use Bluz\Proxy;

/**
 * SQL Source Adapter for Grid package
 *
 * @package  Bluz\Grid
 * @author   Anton Shevchuk
 *
 * @method   string getSource() SQL query
 */
class SqlSource extends AbstractSource
{
    /**
     * Set SQL source
     *
     * @param  string $source
     *
     * @return void
     * @throws GridException
     */
    public function setSource($source): void
    {
        if (!is_string($source)) {
            throw new GridException('Source of `SqlSource` should be string with SQL query');
        }
        parent::setSource($source);
    }

    /**
     * {@inheritdoc}
     */
    public function process(int $page, int $limit, array $filters = [], array $orders = []): Data
    {
        // process filters
        $filters = $this->applyFilters($filters);

        // process orders
        $orders = $this->applyOrders($orders);

        // prepare query
        $type = Proxy\Db::getOption('connect', 'type');

        if (strtolower($type) === 'mysql') {
            // MySQL
            $dataSql = preg_replace('/SELECT\s(.*?)\sFROM/is', 'SELECT SQL_CALC_FOUND_ROWS $1 FROM', $this->source, 1);
            $totalSql = 'SELECT FOUND_ROWS()';
        } else {
            // other
            $dataSql = $this->source;
            $totalSql = preg_replace('/SELECT\s(.*?)\sFROM/is', 'SELECT COUNT(*) FROM', $this->source, 1);
            if (count($filters)) {
                $totalSql .= ' WHERE ' . implode(' AND ', $filters);
            }
        }

        if (count($filters)) {
            $dataSql .= ' WHERE ' . implode(' AND ', $filters);
        }
        if (count($orders)) {
            $dataSql .= ' ORDER BY ' . implode(', ', $orders);
        }
        // process pages
        $dataSql .= ' LIMIT ' . ($page - 1) * $limit . ', ' . $limit;

        // run queries
        // use transaction to avoid errors
        Proxy\Db::transaction(
            function () use (&$data, &$total, $dataSql, $totalSql) {
                $data = Proxy\Db::fetchAll($dataSql);
                $total = (int)Proxy\Db::fetchOne($totalSql);
            }
        );

        $gridData = new Data($data);
        $gridData->setTotal($total);
        return $gridData;
    }

    /**
     * Apply filters to SQL query
     *
     * @param  array[] $settings
     *
     * @return array
     */
    private function applyFilters(array $settings): array
    {
        $where = [];
        foreach ($settings as $column => $filters) {
            foreach ($filters as $filter => $value) {
                if ($filter === Grid\Grid::FILTER_LIKE) {
                    $value = '%' . $value . '%';
                }
                $where[] = $column . ' ' .
                    $this->filters[$filter] . ' ' .
                    Proxy\Db::quote((string)$value);
            }
        }
        return $where;
    }

    /**
     * Apply order to SQL query
     *
     * @param  array $settings
     *
     * @return array
     */
    private function applyOrders(array $settings): array
    {
        $orders = [];
        // Obtain a list of columns
        foreach ($settings as $column => $order) {
            $column = Proxy\Db::quoteIdentifier($column);
            $orders[] = $column . ' ' . $order;
        }
        return $orders;
    }
}
