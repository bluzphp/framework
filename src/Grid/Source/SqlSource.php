<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Grid\Source;

use Bluz\Db;
use Bluz\Grid;
use Bluz\Proxy;

/**
 * SQL Source Adapter for Grid package
 *
 * @package  Bluz\Grid
 *
 * @author   Anton Shevchuk
 * @created  27.08.12 10:10
 */
class SqlSource extends AbstractSource
{
    /**
     * @var string SQL
     */
    protected $source;

    /**
     * setSource
     *
     * @param string $source
     * @throws \Bluz\Grid\GridException
     * @return self
     */
    public function setSource($source)
    {
        if (!is_string($source)) {
            throw new Grid\GridException("Source of `SqlSource` should be string with SQL query");
        }
        $this->source = $source;

        return $this;
    }

    /**
     * process
     *
     * @param array $settings
     * @return \Bluz\Grid\Data
     */
    public function process(array $settings = [])
    {
        // process filters
        $where = [];
        if (!empty($settings['filters'])) {
            foreach ($settings['filters'] as $column => $filters) {
                foreach ($filters as $filter => $value) {
                    if ($filter == Grid\Grid::FILTER_LIKE) {
                        $value = '%'.$value.'%';
                    }
                    $where[] = $column .' '.
                        $this->filters[$filter].' '.
                        Proxy\Db::quote($value);
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
        $connect = Proxy\Config::getData('db', 'connect');
        if (strtolower($connect['type']) == 'mysql') { // MySQL
            $dataSql = preg_replace('/SELECT\s(.*?)\sFROM/is', 'SELECT SQL_CALC_FOUND_ROWS $1 FROM', $this->source, 1);
            $countSql = 'SELECT FOUND_ROWS()';
        } else { // other
            $dataSql = $this->source;
            $countSql = preg_replace('/SELECT\s(.*?)\sFROM/is', 'SELECT COUNT(*) FROM', $this->source, 1);
            if (sizeof($where)) {
                $countSql .= ' WHERE ' . (join(' AND ', $where));
            }
        }

        if (sizeof($where)) {
            $dataSql .= ' WHERE ' . (join(' AND ', $where));
        }
        if (sizeof($orders)) {
            $dataSql .= ' ORDER BY ' . (join(', ', $orders));
        }
        $dataSql .= $limit;

        // run queries
        $data = Proxy\Db::fetchAll($dataSql);
        $total = Proxy\Db::fetchOne($countSql);
        $gridData = new Grid\Data($data);
        $gridData->setTotal($total);
        return $gridData;

    }
}
