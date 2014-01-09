<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Controller;

use Bluz\Application\Exception\ApplicationException;
use Bluz\Crud\AbstractCrud;
use Bluz\Request\AbstractRequest;

/**
 * AbstractController
 *
 * @category Bluz
 * @package  Controller
 *
 * @author   Anton Shevchuk
 * @created  02.10.13 13:52
 */
abstract class AbstractController
{
    /**
     * HTTP Method
     * @var string
     */
    protected $method = AbstractRequest::METHOD_GET;

    /**
     * Params of query
     * @var array
     */
    protected $params = array();

    /**
     * Identifier
     * @var string
     */
    protected $primary;

    /**
     * Query data
     * @var array
     */
    protected $data = array();

    /**
     * @var AbstractCrud
     */
    protected $crud;

    /**
     * Prepare request for processing
     */
    public function __construct()
    {
        $request = app()->getRequest();

        // rewrite REST with "_method" param
        // this is workaround
        $this->method = strtoupper($request->getParam('_method', $request->getMethod()));

        // get all params
        $query = $request->getQuery();

        unset($query['_method']);

        $this->params = $query;

        $this->data = $request->getParams();
    }

    /**
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
     * getData
     *
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
     * Get crud instance
     *
     * @throws \Bluz\Application\Exception\ApplicationException
     * @return AbstractCrud
     */
    public function getCrud()
    {
        if (!$this->crud) {
            $restClass = get_called_class();
            $crudClass = substr($restClass, 0, strrpos($restClass, '\\', 1) + 1) . 'Crud';

            // check class initialization
            if (!is_subclass_of($crudClass, '\Bluz\Crud\AbstractCrud')) {
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
     * Setup Crud instance
     *
     * @param AbstractCrud $crud
     * @return self
     */
    public function setCrud(AbstractCrud $crud)
    {
        $this->crud = $crud;
        return $this;
    }

    /**
     * Get item by primary key(s)
     *
     * @param mixed $primary
     * @return mixed
     */
    public function readOne($primary)
    {
        return $this->getCrud()->readOne($primary);
    }

    /**
     * List of items
     *
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
     *
     * @param array $data
     * @return mixed
     */
    public function createOne($data)
    {
        return $this->getCrud()->createOne($data);
    }

    /**
     * Create items
     *
     * @param array $data
     * @return mixed
     */
    public function createSet($data)
    {
        return $this->getCrud()->createSet($data);
    }

    /**
     * Update item
     *
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
     *
     * @param array $data
     * @return integer
     */
    public function updateSet($data)
    {
        return $this->getCrud()->updateSet($data);
    }

    /**
     * Delete item
     *
     * @param mixed $primary
     * @return integer
     */
    public function deleteOne($primary)
    {
        return $this->getCrud()->deleteOne($primary);
    }

    /**
     * Delete items
     *
     * @param array $data
     * @return integer
     */
    public function deleteSet($data)
    {
        return $this->getCrud()->deleteSet($data);
    }
}
