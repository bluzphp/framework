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
namespace Bluz\Grid;

use Bluz\Common\Helper;
use Bluz\Common\Options;
use Bluz\Proxy\Request;
use Bluz\Proxy\Router;

/**
 * Grid
 *
 * @package  Bluz\Grid
 * @link     https://github.com/bluzphp/framework/wiki/Grid
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
    use Options;
    use Helper;

    const ORDER_ASC = 'asc';
    const ORDER_DESC = 'desc';

    const FILTER_LIKE = 'like'; // like
    const FILTER_ENUM = 'enum'; // one from .., .., ..
    const FILTER_NUM = 'num'; // ==, !=, >, >=, <, <=

    const FILTER_EQ = 'eq'; // equal to ..
    const FILTER_NE = 'ne'; // not equal to ..
    const FILTER_GT = 'gt'; // greater than ..
    const FILTER_GE = 'ge'; // greater than .. or equal
    const FILTER_LT = 'lt'; // less than ..
    const FILTER_LE = 'le'; // less than .. or equal

    /**
     * Instance of Source
     * @var Source\AbstractSource
     */
    protected $adapter;

    /**
     * Instance of Data
     * @var Data
     */
    protected $data;

    /**
     * Unique identification of grid
     * @var string
     */
    protected $uid;

    /**
     * Unique prefix of grid
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
     * Custom array params
     * @var array
     */
    protected $params = array();

    /**
     * Start from 1!
     * @var int
     */
    protected $page = 1;

    /**
     * Limit per page
     * @var int
     */
    protected $limit = 25;

    /**
     * Default value of page limit
     * @var int
     */
    protected $defaultLimit = 25;

    /**
     * Default order
     * @var array
     */
    protected $defaultOrder;

    /**
     * Stack of orders
     *
     * Example
     *     'first' => 'ASC',
     *     'last' => 'ASC'
     * @var array
     */
    protected $orders = array();

    /**
     * Stack of allow orders
     * @var array
     */
    protected $allowOrders = array();

    /**
     * Stack of filters
     * @var array
     */
    protected $filters = array();

    /**
     * Stack of allow filters
     *
     * Example
     *     ['id', 'status' => ['active', 'disable']]
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
            $this->prefix = $this->getUid() . '-';
        } else {
            $this->prefix = '';
        }

        $this->init();

        $this->processRequest();
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
     * Set source adapter
     *
     * @param Source\AbstractSource $adapter
     * @return void
     */
    public function setAdapter(Source\AbstractSource $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Get source adapter
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
     * Get unique Grid Id
     *
     * @return string
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * Get prefix
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Set module
     *
     * @param $module
     * @return void
     */
    public function setModule($module)
    {
        $this->module = $module;
    }

    /**
     * Get module
     *
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Set controller
     *
     * @param $controller
     * @return void
     */
    public function setController($controller)
    {
        $this->controller = $controller;
    }

    /**
     * Get controller
     *
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Process request
     *
     * Example of request url
     * - http://domain.com/pages/grid/
     * - http://domain.com/pages/grid/page/2/
     * - http://domain.com/pages/grid/page/2/order-alias/desc/
     * - http://domain.com/pages/grid/page/2/order-created/desc/order-alias/asc/
     *
     * with prefix for support more than one grid on page
     * - http://domain.com/users/grid/users-page/2/users-order-created/desc/
     * - http://domain.com/users/grid/users-page/2/users-filter-status/active/
     *
     * hash support
     * - http://domain.com/pages/grid/#/page/2/order-created/desc/order-alias/asc/
     *
     * @return Grid
     */
    public function processRequest()
    {
        $this->module = Request::getModule();
        $this->controller = Request::getController();

        $page = Request::getParam($this->prefix . 'page', 1);
        $this->setPage($page);

        $limit = Request::getParam($this->prefix . 'limit', $this->limit);
        $this->setLimit($limit);

        foreach ($this->allowOrders as $column) {
            $order = Request::getParam($this->prefix . 'order-' . $column);
            if ($order) {
                $this->addOrder($column, $order);
            }
        }

        foreach ($this->allowFilters as $column) {
            $filter = Request::getParam($this->prefix . 'filter-' . $column);
            if ($filter) {
                if (strpos($filter, '-')) {
                    $filter = trim($filter, ' -');

                    while ($pos = strpos($filter, '-')) {
                        $filterType = substr($filter, 0, $pos);
                        $filter = substr($filter, $pos + 1);

                        if (strpos($filter, '-')) {
                            $filterValue = substr($filter, 0, strpos($filter, '-'));
                            $filter = substr($filter, strpos($filter, '-') + 1);
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
     * Process source
     *
     * @throws GridException
     * @return self
     */
    public function processSource()
    {
        if (null === $this->adapter) {
            throw new GridException("Grid Adapter is not initiated, please update method init() and try again");
        }

        try {
            $this->data = $this->getAdapter()->process($this->getSettings());
        } catch (\Exception $e) {
            throw new GridException("Grid Adapter can't process request: ". $e->getMessage());
        }

        return $this;
    }

    /**
     * Get data
     *
     * @return Data
     */
    public function getData()
    {
        if (!$this->data) {
            $this->processSource();
        }
        return $this->data;
    }

    /**
     * Get settings
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
     * Setup params
     *
     * @param $params
     * @return void
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * Return params prepared for url builder
     *
     * @param array $rewrite
     * @return array
     */
    public function getParams(array $rewrite = [])
    {
        $params = $this->params;

        // change page
        if (isset($rewrite['page']) && $rewrite['page'] > 1) {
            $params[$this->prefix . 'page'] = $rewrite['page'];
        }

        // change limit
        if (isset($rewrite['limit'])) {
            if ($rewrite['limit'] != $this->defaultLimit) {
                $params[$this->prefix . 'limit'] = ($rewrite['limit'] != $this->limit)
                    ? $rewrite['limit'] : $this->limit;
            }
        } else {
            if ($this->limit != $this->defaultLimit) {
                $params[$this->prefix . 'limit'] = $this->limit;
            }
        }

        // change orders
        if (isset($rewrite['orders'])) {
            $orders = $rewrite['orders'];
        } else {
            $orders = $this->getOrders();
        }

        foreach ($orders as $column => $order) {
            $params[$this->prefix . 'order-' . $column] = $order;
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
                    $columnFilter[] = $filterName . '-' . $filterValue;
                }
            }
            $params[$this->prefix . 'filter-' . $column] = join('-', $columnFilter);
        }

        return $params;
    }

    /**
     * Get Url
     *
     * @param array $params
     * @return string
     */
    public function getUrl($params)
    {
        // prepare params
        $params = $this->getParams($params);

        // retrieve URL
        return Router::getUrl(
            $this->getModule(),
            $this->getController(),
            $params
        );
    }

    /**
     * Set allow orders
     *
     * @param array $orders
     * @return void
     */
    public function setAllowOrders(array $orders = [])
    {
        $this->allowOrders = $orders;
    }

    /**
     * Get allow orders
     *
     * @return array
     */
    public function getAllowOrders()
    {
        return $this->allowOrders;
    }

    /**
     * Add order rule
     *
     * @param string $column
     * @param string $order
     * @throws GridException
     * @return void
     */
    public function addOrder($column, $order = Grid::ORDER_ASC)
    {
        if (!in_array($column, $this->allowOrders)) {
            throw new GridException('Wrong column order');
        }

        if (strtolower($order) != Grid::ORDER_ASC
            && strtolower($order) != Grid::ORDER_DESC
        ) {
            throw new GridException('Order for column "' . $column . '" is incorrect');
        }

        $this->orders[$column] = $order;
    }

    /**
     * Add order rules
     *
     * @param array $orders
     * @return void
     */
    public function addOrders(array $orders)
    {
        foreach ($orders as $column => $order) {
            $this->addOrder($column, $order);
        }
    }

    /**
     * Set order
     *
     * @param string $column
     * @param string $order ASC or DESC
     * @return void
     */
    public function setOrder($column, $order = Grid::ORDER_ASC)
    {
        $this->orders = [];
        $this->addOrder($column, $order);
    }

    /**
     * Set orders
     *
     * @param array $orders
     * @return void
     */
    public function setOrders(array $orders)
    {
        $this->orders = [];
        $this->addOrders($orders);
    }

    /**
     * Get orders
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
     * Set allowed filters
     *
     * @param array $filters
     * @return void
     */
    public function setAllowFilters(array $filters = array())
    {
        $this->allowFilters = $filters;
    }

    /**
     * Get allow filters
     *
     * @return array
     */
    public function getAllowFilters()
    {
        return $this->allowFilters;
    }

    /**
     * Check filter
     *
     * @param string $filter
     * @return bool
     */
    public function checkFilter($filter)
    {
        if ($filter == self::FILTER_EQ or
            $filter == self::FILTER_NE or
            $filter == self::FILTER_GT or
            $filter == self::FILTER_GE or
            $filter == self::FILTER_LT or
            $filter == self::FILTER_LE or
            $filter == self::FILTER_ENUM or
            $filter == self::FILTER_NUM or
            $filter == self::FILTER_LIKE
        ) {
            return true;
        }
        return false;
    }

    /**
     * Add filter
     *
     * @param string $column
     * @param string $filter
     * @param string $value
     * @throws GridException
     * @return void
     */
    public function addFilter($column, $filter, $value)
    {
        if (!in_array($column, $this->allowFilters) &&
            !array_key_exists($column, $this->allowFilters)
        ) {
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
    }


    /**
     * Get filters
     *
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Set page
     *
     * @param int $page
     * @throws GridException
     * @return void
     */
    public function setPage($page = 1)
    {
        if ($page < 1) {
            throw new GridException('Wrong page number, should be greater than zero');
        }
        $this->page = (int)$page;
    }

    /**
     * Get page
     *
     * @return integer
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Set limit per page
     *
     * @param int $limit
     * @throws GridException
     * @return void
     */
    public function setLimit($limit)
    {
        if ($limit < 1) {
            throw new GridException('Wrong limit value, should be greater than zero');
        }
        $this->limit = (int)$limit;
    }

    /**
     * Get limit per page
     *
     * @return integer
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Set default limit
     *
     * @param int $limit
     * @throws GridException
     * @return void
     */
    public function setDefaultLimit($limit)
    {
        if ($limit < 1) {
            throw new GridException('Wrong default limit value, should be greater than zero');
        }
        $this->setLimit($limit);

        $this->defaultLimit = (int)$limit;
    }

    /**
     * Get default limit
     *
     * @return integer
     */
    public function getDefaultLimit()
    {
        return $this->defaultLimit;
    }

    /**
     * Set default order
     *
     * @param string $column
     * @param string $order ASC or DESC
     * @throws GridException
     * @return void
     */
    public function setDefaultOrder($column, $order = Grid::ORDER_ASC)
    {
        $this->setOrder($column, $order);

        $this->defaultOrder = array($column => $order);
    }

    /**
     * Get default order
     *
     * @return array
     */
    public function getDefaultOrder()
    {
        return $this->defaultOrder;
    }
}
