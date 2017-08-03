<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Controller\Mapper;

use Bluz\Application\Application;
use Bluz\Application\Exception\ForbiddenException;
use Bluz\Application\Exception\NotImplementedException;
use Bluz\Controller\Controller;
use Bluz\Controller\ControllerException;
use Bluz\Crud\AbstractCrud;
use Bluz\Http\RequestMethod;
use Bluz\Proxy\Acl;
use Bluz\Proxy\Request;
use Bluz\Proxy\Router;

/**
 * Mapper for controller
 *
 * @package  Bluz\Rest
 * @author   Anton Shevchuk
 */
abstract class AbstractMapper
{
    /**
     * @var string HTTP Method
     */
    protected $method = RequestMethod::GET;

    /**
     * @var string
     */
    protected $module;

    /**
     * @var string
     */
    protected $controller;

    /**
     * @var array identifier
     */
    protected $primary;

    /**
     * @var string relation list
     */
    protected $relation;

    /**
     * @var string relation Id
     */
    protected $relationId;

    /**
     * @var array params of query
     */
    protected $params = [];

    /**
     * @var array query data
     */
    protected $data = [];

    /**
     * @var AbstractCrud instance of CRUD
     */
    protected $crud;

    /**
     * [
     *     METHOD => Link {
     *         'module' => 'module',
     *         'controller' => 'controller',
     *         'acl' => 'privilege',
     *     },
     * ]
     *
     * @var Link[]
     */
    protected $map = [];

    /**
     * Prepare params
     *
     * @return array
     */
    abstract protected function prepareParams(): array;

    /**
     * @param AbstractCrud $crud
     */
    public function __construct(AbstractCrud $crud)
    {
        $this->crud = $crud;
    }

    /**
     * Add mapping data
     *
     * @param string $method
     * @param string $module
     * @param string $controller
     * @return Link
     */
    public function addMap($method, $module, $controller)
    {
        return $this->map[strtoupper($method)] = new Link($module, $controller);
    }

    /**
     * Add mapping for HEAD method
     *
     * @param string $module
     * @param string $controller
     * @return Link
     */
    public function head($module, $controller) : Link
    {
        return $this->addMap(RequestMethod::HEAD, $module, $controller);
    }

    /**
     * Add mapping for GET method
     *
     * @param string $module
     * @param string $controller
     * @return Link
     */
    public function get($module, $controller) : Link
    {
        return $this->addMap(RequestMethod::GET, $module, $controller);
    }

    /**
     * Add mapping for POST method
     *
     * @param string $module
     * @param string $controller
     * @return Link
     */
    public function post($module, $controller) : Link
    {
        return $this->addMap(RequestMethod::POST, $module, $controller);
    }

    /**
     * Add mapping for PATCH method
     *
     * @param string $module
     * @param string $controller
     * @return Link
     */
    public function patch($module, $controller) : Link
    {
        return $this->addMap(RequestMethod::PATCH, $module, $controller);
    }

    /**
     * Add mapping for PUT method
     *
     * @param string $module
     * @param string $controller
     * @return Link
     */
    public function put($module, $controller) : Link
    {
        return $this->addMap(RequestMethod::PUT, $module, $controller);
    }

    /**
     * Add mapping for DELETE method
     *
     * @param string $module
     * @param string $controller
     * @return Link
     */
    public function delete($module, $controller) : Link
    {
        return $this->addMap(RequestMethod::DELETE, $module, $controller);
    }

    /**
     * Add mapping for OPTIONS method
     *
     * @param string $module
     * @param string $controller
     * @return Link
     */
    public function options($module, $controller) : Link
    {
        return $this->addMap(RequestMethod::OPTIONS, $module, $controller);
    }

    /**
     * Run
     *
     * @return Controller
     * @throws ControllerException
     * @throws ForbiddenException
     * @throws NotImplementedException
     */
    public function run() : Controller
    {
        $this->prepareRequest();
        return $this->dispatch();
    }

    /**
     * Prepare request for processing
     *
     * @throws \Bluz\Controller\ControllerException
     */
    protected function prepareRequest()
    {
        // HTTP method
        $method = Request::getMethod();
        $this->method = strtoupper($method);

        // get path
        // %module% / %controller% / %id% / %relation% / %id%
        $path = Router::getCleanUri();

        $this->params = explode('/', rtrim($path, '/'));

        // module
        $this->module = array_shift($this->params);

        // controller
        $this->controller = array_shift($this->params);

        $data = Request::getParams();

        unset($data['_method'], $data['_module'], $data['_controller']);

        $this->data = $data;

        $primary = $this->crud->getPrimaryKey();
        $this->primary = array_intersect_key($this->data, array_flip($primary));
    }

    /**
     * Dispatch REST or CRUD controller
     *
     * @return mixed
     * @throws ForbiddenException
     * @throws NotImplementedException
     */
    protected function dispatch()
    {
        // check implementation
        if (!isset($this->map[$this->method])) {
            throw new NotImplementedException;
        }

        $link = $this->map[$this->method];

        // check permissions
        if (!Acl::isAllowed($this->module, $link->getAcl())) {
            throw new ForbiddenException;
        }

        // setup params
        $link->setParams($this->prepareParams());

        // dispatch controller
        $result = Application::getInstance()->dispatch(
            $link->getModule(),
            $link->getController(),
            $link->getParams()
        );

        return $result;
    }
}
