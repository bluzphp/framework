<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

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

    public const ORDER_ASC = 'asc';
    public const ORDER_DESC = 'desc';

    public const FILTER_LIKE = 'like'; // like
    public const FILTER_ENUM = 'enum'; // one from .., .., ..

    public const FILTER_EQ = 'eq'; // equal to ..
    public const FILTER_NE = 'ne'; // not equal to ..
    public const FILTER_GT = 'gt'; // greater than ..
    public const FILTER_GE = 'ge'; // greater than .. or equal
    public const FILTER_LT = 'lt'; // less than ..
    public const FILTER_LE = 'le'; // less than .. or equal

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
    protected $prefix = '';

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
    protected $params = [];

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
    protected $orders = [];

    /**
     * @var array default order
     * @see Grid::$orders
     */
    protected $defaultOrder = [];

    /**
     * @var array list of allow orders
     * @see Grid::$orders
     */
    protected $allowOrders = [];

    /**
     * @var array list of filters
     */
    protected $filters = [];

    /**
     * List of allow filters
     *
     * Example
     *     ['id', 'status' => ['active', 'disable']]
     *
     * @var array
     * @see Grid::$filters
     */
    protected $allowFilters = [];

    /**
     * List of allow filter names
     *
     * @var array
     * @see Grid::$filters
     */
    protected $allowFilterNames = [
        self::FILTER_LIKE,
        self::FILTER_ENUM,
        self::FILTER_EQ,
        self::FILTER_NE,
        self::FILTER_GT,
        self::FILTER_GE,
        self::FILTER_LT,
        self::FILTER_LE
    ];

    /**
     * List of aliases for columns in DB
     *
     * @var array
     */
    protected $aliases = [];

    /**
     * Grid constructor
     *
     * @param array $options
     *
     * @throws \Bluz\Common\Exception\CommonException
     */
    public function __construct($options = null)
    {
        // initial default helper path
        $this->addHelperPath(__DIR__ . '/Helper/');

        if ($options) {
            $this->setOptions($options);
        }

        if ($this->getUid()) {
            $this->prefix = $this->getUid() . '-';
        }

        $this->init();

        $this->processRequest();
    }

    /**
     * Initialize Grid
     *
     * @return void
     */
    abstract public function init(): void;

    /**
     * Set source adapter
     *
     * @param Source\AbstractSource $adapter
     *
     * @return void
     */
    public function setAdapter(Source\AbstractSource $adapter): void
    {
        $this->adapter = $adapter;
    }

    /**
     * Get source adapter
     *
     * @return Source\AbstractSource
     * @throws GridException
     */
    public function getAdapter(): Source\AbstractSource
    {
        if (null === $this->adapter) {
            throw new GridException('Grid adapter is not initialized');
        }
        return $this->adapter;
    }

    /**
     * Get unique Grid Id
     *
     * @return string
     */
    public function getUid(): string
    {
        return $this->uid;
    }

    /**
     * Get prefix
     *
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * Set module
     *
     * @param string $module
     *
     * @return void
     */
    public function setModule(string $module): void
    {
        $this->module = $module;
    }

    /**
     * Get module
     *
     * @return string
     */
    public function getModule(): ?string
    {
        return $this->module;
    }

    /**
     * Set controller
     *
     * @param string $controller
     *
     * @return void
     */
    public function setController(string $controller): void
    {
        $this->controller = $controller;
    }

    /**
     * Get controller
     *
     * @return string
     */
    public function getController(): ?string
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
     * @return void
     * @throws GridException
     */
    public function processRequest(): void
    {
        $this->module = Request::getModule();
        $this->controller = Request::getController();

        $page = (int)Request::getParam($this->prefix . 'page', 1);
        $this->setPage($page);

        $limit = (int)Request::getParam($this->prefix . 'limit', $this->limit);
        $this->setLimit($limit);

        foreach ($this->allowOrders as $column) {
            $alias = $this->applyAlias($column);
            $order = Request::getParam($this->prefix . 'order-' . $alias);
            if (is_array($order)) {
                $order = current($order);
            }
            if (null !== $order) {
                $this->addOrder($column, $order);
            }
        }
        foreach ($this->allowFilters as $column) {
            $alias = $this->applyAlias($column);
            $filters = (array)Request::getParam($this->prefix . 'filter-' . $alias, []);

            foreach ($filters as $filter) {
                $filter = trim($filter, ' _-');
                if (strpos($filter, '-')) {
                    /**
                     * Example of filters
                     * - http://domain.com/users/grid/users-filter-roleId/gt-2      - roleId greater than 2
                     * - http://domain.com/users/grid/users-filter-roleId/gt-1_lt-4 - 1 < roleId < 4
                     * - http://domain.com/users/grid/users-filter-login/eq-admin   - login == admin
                     * - http://domain.com/users/grid/users-filter-login/like-adm   - login LIKE `adm`
                     * - http://domain.com/users/grid/users-filter-login/like-od-   - login LIKE `od-`
                     */
                    $filters = explode('_', $filter);
                    foreach ($filters as $rawFilter) {
                        [$filterName, $filterValue] = explode('-', $rawFilter, 2);
                        $this->addFilter($column, $filterName, urldecode($filterValue));
                    }
                } else {
                    /**
                     * Example of filters
                     * - http://domain.com/users/grid/users-filter-roleId/2
                     * - http://domain.com/users/grid/users-filter-login/admin
                     */
                    $this->addFilter($column, self::FILTER_EQ, $filter);
                }
            }
        }
    }

    /**
     * Process source
     *
     * @return void
     * @throws GridException
     */
    public function processSource(): void
    {
        if (null === $this->adapter) {
            throw new GridException('Grid Adapter is not initiated, please update method `init()` and try again');
        }

        try {
            $this->data = $this->getAdapter()->process(
                $this->getPage(),
                $this->getLimit(),
                $this->getFilters(),
                $this->getOrders()
            );
        } catch (\Exception $e) {
            throw new GridException('Grid Adapter can\'t process request: '. $e->getMessage());
        }
    }

    /**
     * Get data
     *
     * @return Data
     * @throws \Bluz\Grid\GridException
     */
    public function getData(): Data
    {
        if (!$this->data) {
            $this->processSource();
        }
        return $this->data;
    }

    /**
     * Setup params
     *
     * @param  $params
     *
     * @return void
     */
    public function setParams($params): void
    {
        $this->params = $params;
    }

    /**
     * Return params prepared for url builder
     *
     * @param  array $rewrite
     *
     * @return array
     */
    public function getParams(array $rewrite = []): array
    {
        $params = $this->params;

        // change page to first for each new grid (with new filters or orders, or other stuff)
        $page = $rewrite['page'] ?? 1;

        if ($page > 1) {
            $params[$this->prefix . 'page'] = $page;
        }

        // change limit
        $limit = $rewrite['limit'] ?? $this->getLimit();

        if ($limit !== $this->defaultLimit) {
            $params[$this->prefix . 'limit'] = $limit;
        }

        // change orders
        $orders = $rewrite['orders'] ?? $this->getOrders();

        foreach ($orders as $column => $order) {
            $column = $this->applyAlias($column);
            $params[$this->prefix . 'order-' . $column] = $order;
        }

        // change filters
        $filters = $rewrite['filters'] ?? $this->getFilters();

        foreach ($filters as $column => $columnFilters) {
            /** @var array $columnFilters */
            $column = $this->applyAlias($column);
            if (\count($columnFilters) === 1 && isset($columnFilters[self::FILTER_EQ])) {
                $params[$this->prefix . 'filter-' . $column] = $columnFilters[self::FILTER_EQ];
                continue;
            }

            $columnFilter = [];
            foreach ($columnFilters as $filterName => $filterValue) {
                $columnFilter[] = $filterName . '-' . $filterValue;
            }
            $params[$this->prefix . 'filter-' . $column] = implode('_', $columnFilter);
        }
        return $params;
    }

    /**
     * Get Url
     *
     * @param  array $params
     *
     * @return string
     */
    public function getUrl($params): string
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
     * Add column name for allow order
     *
     * @param string $column
     *
     * @return void
     */
    public function addAllowOrder($column): void
    {
        $this->allowOrders[] = $column;
    }

    /**
     * Set allow orders
     *
     * @param  string[] $orders
     *
     * @return void
     */
    public function setAllowOrders(array $orders = []): void
    {
        $this->allowOrders = [];
        foreach ($orders as $column) {
            $this->addAllowOrder($column);
        }
    }

    /**
     * Get allow orders
     *
     * @return array
     */
    public function getAllowOrders(): array
    {
        return $this->allowOrders;
    }

    /**
     * Check order column
     *
     * @param  string $column
     *
     * @return bool
     */
    protected function checkOrderColumn($column): bool
    {
        return \in_array($column, $this->getAllowOrders(), true);
    }

    /**
     * Check order name
     *
     * @param  string $order
     *
     * @return bool
     */
    protected function checkOrderName($order): bool
    {
        return ($order === self::ORDER_ASC || $order === self::ORDER_DESC);
    }

    /**
     * Add order rule
     *
     * @param  string $column
     * @param  string $order
     *
     * @return void
     * @throws GridException
     */
    public function addOrder($column, $order = self::ORDER_ASC): void
    {
        if (!$this->checkOrderColumn($column)) {
            throw new GridException("Order for column `$column` is not allowed");
        }

        if (!$this->checkOrderName($order)) {
            throw new GridException("Order name for column `$column` is incorrect");
        }

        $this->orders[$column] = $order;
    }

    /**
     * Add order rules
     *
     * @param  array $orders
     *
     * @return void
     * @throws GridException
     */
    public function addOrders(array $orders): void
    {
        foreach ($orders as $column => $order) {
            $this->addOrder($column, $order);
        }
    }

    /**
     * Set order
     *
     * @param  string $column
     * @param  string $order ASC or DESC
     *
     * @return void
     * @throws GridException
     */
    public function setOrder($column, $order = self::ORDER_ASC): void
    {
        $this->orders = [];
        $this->addOrder($column, $order);
    }

    /**
     * Set orders
     *
     * @param  array $orders
     *
     * @return void
     * @throws GridException
     */
    public function setOrders(array $orders): void
    {
        $this->orders = [];
        $this->addOrders($orders);
    }

    /**
     * Get orders
     *
     * @return array
     */
    public function getOrders(): array
    {
        if (empty($this->orders)) {
            return $this->getDefaultOrder();
        }

        return $this->orders;
    }

    /**
     * Add column name to allow filter it
     *
     * @param string $column
     *
     * @return void
     */
    public function addAllowFilter($column): void
    {
        $this->allowFilters[] = $column;
    }

    /**
     * Set allowed filters
     *
     * @param  string[] $filters
     *
     * @return void
     */
    public function setAllowFilters(array $filters = []): void
    {
        $this->allowFilters = [];
        foreach ($filters as $column) {
            $this->addAllowFilter($column);
        }
    }

    /**
     * Get allow filters
     *
     * @return array
     */
    public function getAllowFilters(): array
    {
        return $this->allowFilters;
    }

    /**
     * Check filter column
     *
     * @param  string $column
     *
     * @return bool
     */
    protected function checkFilterColumn($column): bool
    {
        return array_key_exists($column, $this->getAllowFilters()) ||
            \in_array($column, $this->getAllowFilters(), false);
    }

    /**
     * Check filter
     *
     * @param  string $filter
     *
     * @return bool
     */
    protected function checkFilterName($filter): bool
    {
        return \in_array($filter, $this->allowFilterNames, false);
    }

    /**
     * Add filter
     *
     * @param  string $column name
     * @param  string $filter
     * @param  string $value
     *
     * @return void
     * @throws GridException
     */
    public function addFilter($column, $filter, $value): void
    {
        if (!$this->checkFilterColumn($column)) {
            throw new GridException("Filter for column `$column` is not allowed");
        }
        if (!$this->checkFilterName($filter)) {
            throw new GridException('Filter name is incorrect');
        }
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
     *
     * @return mixed
     */
    public function getFilter($column, $filter = null)
    {
        if (null === $filter) {
            return $this->filters[$column] ?? null;
        }
        return $this->filters[$column][$filter] ?? null;
    }

    /**
     * Get filters
     *
     * @return array
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * Add alias for column name
     *
     * @param  string $column
     * @param  string $alias
     *
     * @return void
     */
    public function addAlias($column, $alias): void
    {
        $this->aliases[$column] = $alias;
    }

    /**
     * Get column name by alias
     *
     * @param  string $alias
     *
     * @return string
     */
    protected function reverseAlias($alias): string
    {
        return array_search($alias, $this->aliases, true) ?: $alias;
    }

    /**
     * Get alias by column name
     *
     * @param  string $column
     *
     * @return string
     */
    public function applyAlias($column): string
    {
        return $this->aliases[$column] ?? $column;
    }

    /**
     * Set page
     *
     * @param  integer $page
     *
     * @return void
     * @throws GridException
     */
    public function setPage(int $page = 1): void
    {
        if ($page < 1) {
            throw new GridException('Wrong page number, should be greater than zero');
        }
        $this->page = $page;
    }

    /**
     * Get page
     *
     * @return integer
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * Set limit per page
     *
     * @param  integer $limit
     *
     * @return void
     * @throws GridException
     */
    public function setLimit(int $limit): void
    {
        if ($limit < 1) {
            throw new GridException('Wrong limit value, should be greater than zero');
        }
        $this->limit = $limit;
    }

    /**
     * Get limit per page
     *
     * @return integer
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * Set default limit
     *
     * @param  integer $limit
     *
     * @return void
     * @throws GridException
     */
    public function setDefaultLimit(int $limit): void
    {
        if ($limit < 1) {
            throw new GridException('Wrong default limit value, should be greater than zero');
        }
        $this->setLimit($limit);

        $this->defaultLimit = $limit;
    }

    /**
     * Get default limit
     *
     * @return integer
     */
    public function getDefaultLimit(): int
    {
        return $this->defaultLimit;
    }

    /**
     * Set default order
     *
     * @param  string $column
     * @param  string $order ASC or DESC
     *
     * @return void
     */
    public function setDefaultOrder($column, $order = self::ORDER_ASC): void
    {
        $this->defaultOrder = [$column => $order];
    }

    /**
     * Get default order
     *
     * @return array
     */
    public function getDefaultOrder(): ?array
    {
        return $this->defaultOrder;
    }
}
