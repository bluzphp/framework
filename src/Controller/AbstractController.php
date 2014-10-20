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
 * AbstractController
 *
 * @package  Bluz\Controller
 *
 * @author   Anton Shevchuk
 * @created  02.10.13 13:52
 */
abstract class AbstractController
{
    /**
     * @var string HTTP Method
     */
    protected $method = Request::METHOD_GET;

    /**
     * @var array Params of query
     */
    protected $params = array();

    /**
     * @var array Identifier
     */
    protected $primary;

    /**
     * @var array Query data
     */
    protected $data = array();

    /**
     * @var AbstractCrud Instance of CRUD
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
        $query = Request::getQuery();

        unset($query['_method']);

        $this->params = $query;

        $this->data = Request::getParams();
    }

    /**
     * Controller should be executable
     * @return mixed
     */
    abstract public function __invoke();

    /**
     * Return HTTP request method
     * Can be rewrite by '_method' parameter
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Get Data
     * @param string $field
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
     * @param AbstractCrud $crud
     * @return self
     */
    public function setCrud(AbstractCrud $crud)
    {
        $this->crud = $crud;
        return $this;
    }

    /**
     * Get crud instance
     * @throws \Bluz\Application\Exception\ApplicationException
     * @return AbstractCrud
     */
    public function getCrud()
    {
        if (!$this->crud) {
            $controllerClass = get_called_class();
            $crudClass = substr($controllerClass, 0, strrpos($controllerClass, '\\', 1) + 1) . 'Crud';

            // check class initialization
            if (!class_exists($crudClass) or !is_subclass_of($crudClass, '\\Bluz\\Crud\\AbstractCrud')) {
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
     * @param mixed $primary
     * @return mixed
     */
    public function readOne($primary)
    {
        return $this->getCrud()->readOne($primary);
    }

    /**
     * List of items
     * @param int $offset
     * @param int $limit
     * @param array $params
     * @return mixed
     */
    public function readSet($offset = 0, $limit = AbstractCrud::DEFAULT_LIMIT, $params = array())
    {
        return $this->getCrud()->readSet($offset, $limit, $params);
    }

    /**
     * Create new item
     * @param array $data
     * @return mixed
     */
    public function createOne($data)
    {
        return $this->getCrud()->createOne($data);
    }

    /**
     * Create items
     * @param array $data
     * @return mixed
     */
    public function createSet($data)
    {
        return $this->getCrud()->createSet($data);
    }

    /**
     * Update item
     * @param mixed $id
     * @param array $data
     * @return integer
     */
    public function updateOne($id, $data)
    {
        return $this->getCrud()->updateOne($id, $data);
    }

    /**
     * Update items
     * @param array $data
     * @return integer
     */
    public function updateSet($data)
    {
        return $this->getCrud()->updateSet($data);
    }

    /**
     * Delete item
     * @param mixed $primary
     * @return integer
     */
    public function deleteOne($primary)
    {
        return $this->getCrud()->deleteOne($primary);
    }

    /**
     * Delete items
     * @param array $data
     * @return integer
     */
    public function deleteSet($data)
    {
        return $this->getCrud()->deleteSet($data);
    }
}
