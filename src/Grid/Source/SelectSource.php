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
class SelectSource extends AbstractSource
{
    /**
     * @var Db\Query\Select instance
     */
    protected $source;

    /**
     * setSource
     *
     * @param Db\Query\Select $source
     * @throws \Bluz\Grid\GridException
     * @return self
     */
    public function setSource($source)
    {
        if (!$source instanceof Db\Query\Select) {
            throw new Grid\GridException("Source of `SelectSource` should be `Db\\Query\\Select` object");
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
        if (!empty($settings['filters'])) {
            foreach ($settings['filters'] as $column => $filters) {
                foreach ($filters as $filter => $value) {
                    if ($filter == Grid\Grid::FILTER_LIKE) {
                        $value = '%'.$value.'%';
                    }
                    $this->source->andWhere($column .' '. $this->filters[$filter] .' ?', $value);
                }
            }
        }

        // process orders
        if (!empty($settings['orders'])) {
            // Obtain a list of columns
            foreach ($settings['orders'] as $column => $order) {
                $this->source->addOrderBy($column, $order);
            }
        }

        // process pages
        $this->source->setLimit($settings['limit']);
        $this->source->setPage($settings['page']);

        // prepare query
        $connect = Proxy\Config::getData('db', 'connect');
        if (strtolower($connect['type']) == 'mysql') {
            // MySQL
            $select = $this->source->getQueryPart('select');
            $select[0] = 'SQL_CALC_FOUND_ROWS ' . current($select);
            $this->source->select($select);

            // run queries
            $data = $this->source->execute();
            $total = Proxy\Db::fetchOne('SELECT FOUND_ROWS()');
        } else {
            // other
            $totalSource = clone $this->source;
            $totalSource->select('COUNT(*)');

            // run queries
            $data = $this->source->execute();
            $total = $totalSource->execute();
        }
        $gridData = new Grid\Data($data);
        $gridData->setTotal($total);
        return $gridData;

    }
}
