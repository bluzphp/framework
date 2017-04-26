<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Grid\Source;

use Bluz\Db;
use Bluz\Grid;
use Bluz\Proxy;

/**
 * SQL Source Adapter for Grid package
 *
 * @package  Bluz\Grid
 * @author   Anton Shevchuk
 */
class SqlSource extends AbstractSource
{
    /**
     * @var string SQL
     */
    protected $source;

    /**
     * Set SQL source
     *
     * @param  string $source
     * @return self
     * @throws \Bluz\Grid\GridException
     */
    public function setSource($source)
    {
        if (!is_string($source)) {
            throw new Grid\GridException('Source of `SqlSource` should be string with SQL query');
        }
        $this->source = $source;

        return $this;
    }

    /**
     * Process
     *
     * @param  array[] $settings
     * @return \Bluz\Grid\Data
     */
    public function process(array $settings = [])
    {
        // process filters
        $where = [];
        if (!empty($settings['filters'])) {
            foreach ($settings['filters'] as $column => $filters) {
                foreach ($filters as $filter => $value) {
                    if ($filter === Grid\Grid::FILTER_LIKE) {
                        $value = '%'.$value.'%';
                    }
                    $where[] = $column .' '.
                        $this->filters[$filter].' '.
                        Proxy\Db::quote((string)$value);
                }
            }
        }

        // process orders
        $orders = [];
        if (!empty($settings['orders'])) {
            // Obtain a list of columns
            foreach ($settings['orders'] as $column => $order) {
                $column = Proxy\Db::quoteIdentifier($column);
                $orders[] = $column . ' ' . $order;
            }
        }

        // process pages
        $limit = ' LIMIT ' . ($settings['page'] - 1) * $settings['limit'] . ', ' . $settings['limit'];

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
            if (count($where)) {
                $totalSql .= ' WHERE ' . implode(' AND ', $where);
            }
        }

        if (count($where)) {
            $dataSql .= ' WHERE ' . implode(' AND ', $where);
        }
        if (count($orders)) {
            $dataSql .= ' ORDER BY ' . implode(', ', $orders);
        }
        $dataSql .= $limit;

        // run queries
        // use transaction to avoid errors
        Proxy\Db::transaction(function () use (&$data, &$total, $dataSql, $totalSql) {
            $data = Proxy\Db::fetchAll($dataSql);
            $total = (int) Proxy\Db::fetchOne($totalSql);
        });
        
        $gridData = new Grid\Data($data);
        $gridData->setTotal($total);
        return $gridData;
    }
}
