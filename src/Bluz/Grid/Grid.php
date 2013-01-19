<?php
/**
 * Copyright (c) 2012 by Bluz PHP Team
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * @namespace
 */
namespace Bluz\Grid;

use Bluz\Application;

/**
 * Grid
 *
 * @category Bluz
 * @package  Grid
 *
 * @method string filter($column, $filter, $value, $reset = true)
 * @method string first()
 * @method string last()
 * @method string limit($limit = 25)
 * @method string next()
 * @method string order($column, $order = null, $defaultOrder = Grid::ORDER_ASC, $reset = true)
 * @method string page($page = 1)
 * @method string pages()
 * @method string prev()
 * @method string reset()
 * @method string total()
 *
 * @author   Anton Shevchuk
 * @created  15.08.12 11:52
 */
abstract class Grid
{
    use \Bluz\Package;
    use \Bluz\Helper;

    const ORDER_ASC = 'asc';
    const ORDER_DESC = 'desc';

    const FILTER_ENUM = 'enum'; // one from .., .., ..
    const FILTER_NUM = 'num';   // ==, !=, >, >=, <, <=

    const FILTER_EQ = 'eq'; // equal to ..
    const FILTER_NE = 'ne'; // not equal to ..
    const FILTER_GT = 'gt'; // greater than ..
    const FILTER_GE = 'ge'; // greater than .. or equal
    const FILTER_LT = 'lt'; // less than ..
    const FILTER_LE = 'le'; // less than .. or equal

    /**
     * @var Source\AbstractSource
     */
    protected $adapter;

    /**
     * @var Data
     */
    protected $data;

    /**
     * Unique identification of grid
     *
     * @var string
     */
    protected $uid;

    /**
     * Unique prefix of grid
     *
     * @var string
     */
    protected $prefix;

    /**
     * Location of Grid
     * @var string $module
     */
    protected $module;

    /**
     * Location of Grid
     * @var string $controller
     */
    protected $controller;

    /**
     * Start from 1!
     *
     * @var int
     */
    protected $page = 1;

    /**
     * @var int
     */
    protected $limit = 25;

    /**
     * @var int
     */
    protected $defaultLimit = 25;

    /**
     * @var string
     */
    protected $defaultOrder;

    /**
     * <pre>
     * <code>
     * [
     *     'first' => 'ASC',
     *     'last' => 'ASC'
     * ]
     * </code>
     * </pre>
     * @var array
     */
    protected $orders = array();

    /**
     * <pre>
     * <code>
     * ['first', 'last', 'email']
     * </code>
     * </pre>
     * @var array
     */
    protected $allowOrders = array();

    /**
     * @var array
     */
    protected $filters = array();

    /**
     * <pre>
     * <code>
     * ['id', 'status' => ['active', 'disable']]
     * </code>
     * </pre>
     *
     * @var array
     */
    protected $allowFilters = array();

    /**
     * __construct
     *
     * @param array $options
     * @return Grid
     */
    public function __construct($options = null)
    {
        if ($options) {
            $this->setOptions($options);
        }

        if ($this->uid) {
            $this->prefix = $this->getUid() .'-';
        } else {
            $this->prefix = '';
        }

        $this->init();

        $this->processRequest();
        $this->processSource();
        // initial default helper path
        $this->addHelperPath(dirname(__FILE__) . '/Helper/');
    }

    /**
     * init
     *
     * @return Grid
     */
    abstract public function init();

    /**
     * setAdapter
     *
     * @param Source\AbstractSource $adapter
     * @return Grid
     */
    public function setAdapter(Source\AbstractSource $adapter)
    {
        $this->adapter = $adapter;
        return $this;
    }

    /**
     * getAdapter
     *
     * @throws GridException
     * @return Source\AbstractSource
     */
    public function getAdapter()
    {
        if (null == $this->adapter) {
            throw new GridException('Grid adapter is not initialized');
        }
        return $this->adapter;
    }

    /**
     * getUid
     * 
     * @return string
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * getPrefix
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * setModule
     *
     * @param $module
     * @return self
     */
    public function setModule($module)
    {
        $this->module = $module;
        return $this;
    }

    /**
     * getModule
     *
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * setController
     *
     * @param $controller
     * @return self
     */
    public function setController($controller)
    {
        $this->controller = $controller;
        return $this;
    }

    /**
     * getController
     *
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * process request
     *
     * <code>
     * // example of request url
     * // http://domain.com/pages/grid/
     * // http://domain.com/pages/grid/page/2/
     * // http://domain.com/pages/grid/page/2/order-alias/desc/
     * // http://domain.com/pages/grid/page/2/order-created/desc/order-alias/asc/
     *
     * // with prefix for support more than one grid on page
     * // http://domain.com/users/grid/users-page/2/users-order-created/desc/
     * // http://domain.com/users/grid/users-page/2/users-filter-status/active/
     *
     * // hash support
     * // http://domain.com/pages/grid/#/page/2/order-created/desc/order-alias/asc/
     *
     * </code>
     *
     * @return Grid
     */
    public function processRequest()
    {
        $request = $this->getApplication()->getRequest();

        $this->module = $request->getModule();
        $this->controller = $request->getController();

        $page = $request->getParam($this->prefix.'page', 1);
        $this->setPage($page);

        $limit = $request->getParam($this->prefix.'limit', $this->limit);
        $this->setLimit($limit);

        foreach ($this->allowOrders as $column) {
            $order = $request->getParam($this->prefix.'order-'.$column);
            if ($order) {
                $this->addOrder($column, $order);
            }
        }

        foreach ($this->allowFilters as $column) {
            $filter = $request->getParam($this->prefix.'filter-'.$column);
            if ($filter) {
                if (strpos($filter, '-')) {
                    $filter = trim($filter, ' -');

                    while ($pos = strpos($filter, '-')) {

                        $filterType = substr($filter, 0, $pos);
                        $filter = substr($filter, $pos+1);

                        if ($pos = strpos($filter, '-')) {
                            $filterValue = substr($filter, 0, strpos($filter, '-'));
                            $filter = substr($filter, strpos($filter, '-')+1);
                        } else {
                            $filterValue = $filter;
                        }

                        $this->addFilter($column, $filterType, $filterValue);
                    }

                } else {
                    $this->addFilter($column, self::FILTER_EQ, $filter);
                }
            }
        }
        return $this;
    }

    /**
     * processSource
     *
     * @throws GridException
     * @return self
     */
    public function processSource()
    {
        if (null === $this->adapter) {
            throw new GridException("Grid Adapter is not initiated, please change method init() and try again");
        }

        $this->data = $this->getAdapter()->process($this->getSettings());
        
        return $this;
    }
    
    /**
     * getData
     * 
     * @return Data
     */
    public function getData()
    {
        return $this->data;
    }
    
    /**
     * getSettings
     * 
     * @return array
     */
    public function getSettings()
    {
        $settings = array();
        $settings['page'] = $this->getPage();
        $settings['limit'] = $this->getLimit();
        $settings['orders'] = $this->getOrders();
        $settings['filters'] = $this->getFilters();
        return $settings;
    }

    /**
     * return params prepared for url builder
     *
     * @param array $rewrite
     * @return array
     */
    public function getParams(array $rewrite = [])
    {
        $params = array();

        // change page
        if (isset($rewrite['page']) && $rewrite['page'] > 1) {
            $params[$this->prefix.'page'] = $rewrite['page'];
        }

        // change limit
        if (isset($rewrite['limit'])) {
            if ($rewrite['limit'] != $this->defaultLimit) {
                $params[$this->prefix.'limit'] = ($rewrite['limit']!=$this->limit)?$rewrite['limit']:$this->limit;
            }
        } else {
            if ($this->limit != $this->defaultLimit) {
                $params[$this->prefix.'limit'] = $this->limit;
            }
        }

        // change orders
        if (isset($rewrite['orders'])) {
            $orders = $rewrite['orders'];
        } else {
            $orders = $this->getOrders();
        }

        foreach($orders as $column => $order) {
            $params[$this->prefix.'order-'.$column] = $order;
        }

        // change filters
        if (isset($rewrite['filters'])) {
            $filters = $rewrite['filters'];
        } else {
            $filters = $this->getFilters();
        }
        foreach ($filters as $column => $columnFilters) {
            $columnFilter = [];
            foreach ($columnFilters as $filterName => $filterValue) {
                if ($filterName == self::FILTER_EQ) {
                    $columnFilter[] = $filterValue;
                } else {
                    $columnFilter[] = $filterName .'-'. $filterValue;
                }
            }
            $params[$this->prefix.'filter-'.$column] = join('-', $columnFilter);
        }

        return $params;
    }

    /**
     * getUrl
     *
     * @param array $params
     * @return string
     */
    public function getUrl($params)
    {
        // prepare params
        $params = $this->getParams($params);

        // retrieve URL
        return $this->getApplication()->getRouter()->url(
            $this->getModule(),
            $this->getController(),
            $params
        );
    }

    /**
     * setAllowOrders
     *
     * @param array $orders
     * @return Grid
     */
    public function setAllowOrders(array $orders = [])
    {
        $this->allowOrders = $orders;
        return $this;
    }
    
    /**
     * getAllowOrders
     * 
     * @return array
     */
    public function getAllowOrders()
    {
        return $this->allowOrders;
    }

    /**
     * @param        $column
     * @param string $order
     * @throws GridException
     * @return Grid
     */
    public function addOrder($column, $order = Grid::ORDER_ASC)
    {
        if (!in_array($column, $this->allowOrders)) {
            throw new GridException('Wrong column order');
        }

        if (strtolower($order) != Grid::ORDER_ASC
            && strtolower($order) != Grid::ORDER_DESC) {
            throw new GridException('Order for column "'.$column.'" is incorrect');
        }

        $this->orders[$column] = $order;

        return $this;
    }

    /**
     * @param array $orders
     * @return Grid
     */
    public function addOrders(array $orders)
    {
        foreach ($orders as $column => $order) {
            $this->addOrder($column, $order);
        }
        return $this;
    }

    /**
     * @param        $column
     * @param string $order
     * @return Grid
     */
    public function setOrder($column, $order = Grid::ORDER_ASC)
    {
        $this->orders = [];
        $this->addOrder($column, $order);
        return $this;
    }

    /**
     * @param array $orders
     * @return Grid
     */
    public function setOrders(array $orders)
    {
        $this->orders = [];
        foreach ($orders as $column => $order) {
            $this->addOrder($column, $order);
        }
        return $this;
    }

    /**
     * getOrders
     * 
     * @return array
     */
    public function getOrders()
    {
        $default = $this->getDefaultOrder();

        // remove default order when another one is set
        if (is_array($default)
            && count($this->orders) > 1
            && isset($this->orders[key($default)])
            && $this->orders[key($default)] == reset($default)
        ) {
            unset($this->orders[key($default)]);
        }

        return $this->orders;
    }

    /**
     * setAllowedFilters
     *
     * @param array $filters
     * @return self
     */
    public function setAllowFilters(array $filters = array())
    {
        $this->allowFilters = $filters;
        return $this;
    }
    
    /**
     * getAllowedFilters
     * 
     * @return array
     */
    public function getAllowFilters()
    {
        return $this->allowFilters;
    }

    /**
     * checkFilter
     *
     * @param $filter
     * @return boolean
     */
    public function checkFilter($filter)
    {
        if ($filter == self::FILTER_EQ or
            $filter == self::FILTER_NE or
            $filter == self::FILTER_GT or
            $filter == self::FILTER_GE or
            $filter == self::FILTER_LT or
            $filter == self::FILTER_LE
        ) {
            return true;
        }
        return false;
    }

    /**
     * addFilter
     *
     * @param string $column
     * @param string $filter
     * @param string $value
     * @throws GridException
     * @return self
     */
    public function addFilter($column, $filter, $value)
    {
        if (!in_array($column, $this->allowFilters) &&
            !array_key_exists($column, $this->allowFilters)) {
            throw new GridException('Wrong column name for filter');
        }

        $filter = strtolower($filter);

        if (!$this->checkFilter($filter)) {
            throw new GridException('Wrong filter name');
        }

        if (!isset($this->filters[$column])) {
            $this->filters[$column] = [];
        }
        $this->filters[$column][$filter] = $value;
        return $this;
    }


    /**
     * getFilters
     * 
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * setPage
     *
     * @param int $page
     * @throws GridException
     * @return Grid
     */
    public function setPage($page = 1)
    {
        if ($page < 1) {
            throw new GridException('Wrong page number, should be greater than zero');
        }
        $this->page = (int) $page;
        return $this;
    }

    /**
     * getPage
     * 
     * @return integer
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * setLimit
     *
     * @param int $limit
     * @throws GridException
     * @return Grid
     */
    public function setLimit($limit)
    {
        if ($limit < 1) {
            throw new GridException('Wrong limit value, should be greater than zero');
        }
        $this->limit = (int) $limit;
        return $this;
    }

    /**
     * getLimit
     * 
     * @return integer
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * setDefaultLimit
     *
     * @param int $limit
     * @throws GridException
     * @return Grid
     */
    public function setDefaultLimit($limit)
    {
        if ($limit < 1) {
            throw new GridException('Wrong default limit value, should be greater than zero');
        }
        $this->setLimit($limit);

        $this->defaultLimit = (int) $limit;
        return $this;
    }

    /**
     * getDefaultLimit
     *
     * @return integer
     */
    public function getDefaultLimit()
    {
        return $this->defaultLimit;
    }

    /**
     * setDefaultOrder
     *
     * @param string $column
     * @param string $order
     * @throws GridException
     * @return Grid
     */
    public function setDefaultOrder($column, $order = Grid::ORDER_ASC)
    {
        if (empty($column)) {
            throw new GridException('Wrong default order value, should be not empty');
        }
        $this->setOrder($column, $order);

        $this->defaultOrder = array($column => $order);
        return $this;
    }

    /**
     * getDefaultOrder
     *
     * @return integer
     */
    public function getDefaultOrder()
    {
        return $this->defaultOrder;
    }


}
