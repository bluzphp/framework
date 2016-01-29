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
namespace Bluz\Controller;

use Bluz\Application\Exception\ApplicationException;
use Bluz\Crud\AbstractCrud;
use Bluz\Proxy\Request;

/**
 * Abstract Controller
 *
 * @package  Bluz\Controller
 * @author   Anton Shevchuk
 */
abstract class AbstractController
{
    /**
     * @var string HTTP Method
     */
    protected $method = Request::METHOD_GET;

    /**
     * @var array params of query
     */
    protected $params = array();

    /**
     * @var array identifier
     */
    protected $primary;

    /**
     * @var array query data
     */
    protected $data = array();

    /**
     * @var AbstractCrud instance of CRUD
     */
    protected $crud;

    /**
     * Prepare request for processing
     */
    public function __construct()
    {
        // rewrite REST with "_method" param
        // this is workaround
        $this->method = strtoupper(Request::getParam('_method', Request::getMethod()));

        // get all params
        $query = Request::getQueryParams();

        if (is_array($query) && !empty($query)) {
            unset($query['_method']);
            $this->params = $query;
        }

        $data = Request::getParams();
        unset($data['_method'], $data['_module'], $data['_controller']);

        $this->data = $data;
    }

    /**
     * Controller should be executable
     *
     * @return mixed
     */
    abstract public function __invoke();

    /**
     * Return HTTP request method
     * Can be rewrite by '_method' parameter
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Get Data
     *
     * @param  string $field
     * @return array
     */
    public function getData($field = null)
    {
        if (null !== $field) {
            if (isset($this->data[$field])) {
                return $this->data[$field];
            } else {
                return null;
            }
        }

        return $this->data;
    }

    /**
     * Setup Crud instance
     *
     * @param  AbstractCrud $crud
     * @return self
     */
    public function setCrud(AbstractCrud $crud)
    {
        $this->crud = $crud;
        return $this;
    }

    /**
     * Get crud instance
     *
     * @return AbstractCrud
     * @throws \Bluz\Application\Exception\ApplicationException
     */
    public function getCrud()
    {
        if (!$this->crud) {
            $controllerClass = static::class;
            $crudClass = substr($controllerClass, 0, strrpos($controllerClass, '\\', 1) + 1) . 'Crud';

            // check class initialization
            if (!class_exists($crudClass) || !is_subclass_of($crudClass, '\\Bluz\\Crud\\AbstractCrud')) {
                throw new ApplicationException("`Crud` class is not exists or not initialized");
            }

            /**
             * @var AbstractCrud $crudClass
             */
            $crud = $crudClass::getInstance();

            $this->setCrud($crud);
        }
        return $this->crud;
    }

    /**
     * Get item by primary key(s)
     *
     * @param  mixed $primary
     * @return mixed
     */
    public function readOne($primary)
    {
        return $this->getCrud()->readOne($primary);
    }

    /**
     * List of items
     *
     * @param  integer $offset
     * @param  integer $limit
     * @param  array   $params
     * @return mixed
     */
    public function readSet($offset = 0, $limit = AbstractCrud::DEFAULT_LIMIT, $params = array())
    {
        return $this->getCrud()->readSet($offset, $limit, $params);
    }

    /**
     * Create new item from array
     *
     * @param  array $data
     * @return mixed
     */
    public function createOne($data)
    {
        return $this->getCrud()->createOne($data);
    }

    /**
     * Create items from two-level array
     *
     * @param  array $data
     * @return mixed
     */
    public function createSet($data)
    {
        return $this->getCrud()->createSet($data);
    }

    /**
     * Update item from array
     *
     * @param  mixed $id
     * @param  array $data
     * @return integer
     */
    public function updateOne($id, $data)
    {
        return $this->getCrud()->updateOne($id, $data);
    }

    /**
     * Update items from arrays
     *
     * @param  array $data
     * @return integer
     */
    public function updateSet($data)
    {
        return $this->getCrud()->updateSet($data);
    }

    /**
     * Delete item
     *
     * @param  mixed $primary
     * @return integer
     */
    public function deleteOne($primary)
    {
        return $this->getCrud()->deleteOne($primary);
    }

    /**
     * Delete items by array of IDs
     *
     * @param  array $data
     * @return integer
     */
    public function deleteSet($data)
    {
        return $this->getCrud()->deleteSet($data);
    }
}
