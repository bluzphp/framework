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
 * @author   Anton Shevchuk
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
     * @var Source\AbstractSource instance of Source
     */
    protected $adapter;

    /**
     * @var Data instance of Data
     */
    protected $data;

    /**
     * @var string unique identification of grid
     */
    protected $uid;

    /**
     * @var string unique prefix of grid
     */
    protected $prefix;

    /**
     * @var string location of Grid
     */
    protected $module;

    /**
     * @var string location of Grid
     */
    protected $controller;

    /**
     * @var array custom array params
     */
    protected $params = array();

    /**
     * @var integer start from first page
     */
    protected $page = 1;

    /**
     * @var integer limit per page
     */
    protected $limit = 25;

    /**
     * @var integer default value of page limit
     * @see Grid::$limit
     */
    protected $defaultLimit = 25;

    /**
     * List of orders
     *
     * Example
     *     'first' => 'ASC',
     *     'last' => 'ASC'
     *
     * @var array
     */
    protected $orders = array();

    /**
     * @var array default order
     * @see Grid::$orders
     */
    protected $defaultOrder;

    /**
     * @var array list of allow orders
     * @see Grid::$orders
     */
    protected $allowOrders = array();

    /**
     * @var array list of filters
     */
    protected $filters = array();

    /**
     * List of allow filters
     *
     * Example
     *     ['id', 'status' => ['active', 'disable']]
     *
     * @var array
     * @see Grid::$filters
     */
    protected $allowFilters = array();

    /**
     * List of aliases for columns in DB
     *
     * @var array
     */
    protected $aliases = array();

    /**
     * Grid constructor
     *
     * @param array $options
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
     * Initialize Grid
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
     * @return Source\AbstractSource
     * @throws GridException
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
     * @param string $module
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
     * @param  string $controller
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
     * @return self
     * @throws GridException
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
     * @param  $params
     * @return void
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * Return params prepared for url builder
     *
     * @param  array $rewrite
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
     * @param  array $params
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
     * @param  string[] $orders
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
     * @param  string $column
     * @param  string $order
     * @return void
     * @throws GridException
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
        $column = $this->applyAlias($column);

        $this->orders[$column] = $order;
    }

    /**
     * Add order rules
     *
     * @param  array $orders
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
     * @param  string $column
     * @param  string $order  ASC or DESC
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
     * @param  array $orders
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
     * @param  string[] $filters
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
     * @param  string $filter
     * @return bool
     */
    public function checkFilter($filter)
    {
        if ($filter == self::FILTER_EQ ||
            $filter == self::FILTER_NE ||
            $filter == self::FILTER_GT ||
            $filter == self::FILTER_GE ||
            $filter == self::FILTER_LT ||
            $filter == self::FILTER_LE ||
            $filter == self::FILTER_NUM ||
            $filter == self::FILTER_ENUM ||
            $filter == self::FILTER_LIKE
        ) {
            return true;
        }
        return false;
    }

    /**
     * Add filter
     *
     * @param  string $column
     * @param  string $filter
     * @param  string $value
     * @return void
     * @throws GridException
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

        $column = $this->applyAlias($column);
        
        if (!isset($this->filters[$column])) {
            $this->filters[$column] = [];
        }
        $this->filters[$column][$filter] = $value;
    }


    /**
     * Get filter
     *
     * @param  string $column
     * @param  string $filter
     * @return mixed
     */
    public function getFilter($column, $filter = null)
    {
        if (isset($this->filters[$column])) {
            if ($filter) {
                if (isset($this->filters[$column][$filter])) {
                    return $this->filters[$column][$filter];
                } else {
                    return null;
                }
            } else {
                return $this->filters[$column];
            }
        } else {
            return null;
        }
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
     * Add alias
     *
     * @param  string $key
     * @param  string $value
     * @return void
     */
    public function addAlias($key, $value)
    {
        $this->aliases[$key] = $value;
    }

    /**
     * Set aliases
     *
     * @param array $aliases
     * @return void
     */
    public function setAliases($aliases)
    {
        $this->aliases = $aliases;
    }

    /**
     * Apply Alias
     *
     * @param  string $key
     * @return string
     */
    protected function applyAlias($key)
    {
        return isset($this->aliases[$key])?$this->aliases[$key]:$key;
    }

    /**
     * Set page
     *
     * @param  integer $page
     * @return void
     * @throws GridException
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
     * @param  integer $limit
     * @return void
     * @throws GridException
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
     * @param  integer $limit
     * @return void
     * @throws GridException
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
     * @param  string $column
     * @param  string $order ASC or DESC
     * @return void
     * @throws GridException
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
