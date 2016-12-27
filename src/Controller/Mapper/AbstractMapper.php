<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Controller\Mapper;

use Bluz\Application\Application;
use Bluz\Application\Exception\ForbiddenException;
use Bluz\Application\Exception\NotImplementedException;
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
     *     METHOD => [
     *         'module' => 'module',
     *         'controller' => 'controller',
     *         'acl' => 'privilege',
     *     ],
     * ]
     *
     * @var array
     */
    protected $map = [];

    /**
     * Prepare request for processing
     */
    public function __construct()
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
    }

    /**
     * Add mapping data
     *
     * @param string $method
     * @param string $module
     * @param string $controller
     * @param String $acl
     */
    public function addMap($method, $module, $controller, $acl = null)
    {
        $this->map[strtoupper($method)] = [
            'module' => $module,
            'controller' => $controller,
            'acl' => $acl
        ];
    }

    /**
     * Add mapping for HEAD method
     *
     * @param string $module
     * @param string $controller
     * @param String $acl
     */
    public function head($module, $controller, $acl = null)
    {
        $this->addMap(RequestMethod::HEAD, $module, $controller, $acl);
    }

    /**
     * Add mapping for GET method
     *
     * @param string $module
     * @param string $controller
     * @param String $acl
     */
    public function get($module, $controller, $acl = null)
    {
        $this->addMap(RequestMethod::GET, $module, $controller, $acl);
    }

    /**
     * Add mapping for POST method
     *
     * @param string $module
     * @param string $controller
     * @param String $acl
     */
    public function post($module, $controller, $acl = null)
    {
        $this->addMap(RequestMethod::POST, $module, $controller, $acl);
    }

    /**
     * Add mapping for PATCH method
     *
     * @param string $module
     * @param string $controller
     * @param String $acl
     */
    public function patch($module, $controller, $acl = null)
    {
        $this->addMap(RequestMethod::PATCH, $module, $controller, $acl);
    }

    /**
     * Add mapping for PUT method
     *
     * @param string $module
     * @param string $controller
     * @param String $acl
     */
    public function put($module, $controller, $acl = null)
    {
        $this->addMap(RequestMethod::PUT, $module, $controller, $acl);
    }

    /**
     * Add mapping for DELETE method
     *
     * @param string $module
     * @param string $controller
     * @param String $acl
     */
    public function delete($module, $controller, $acl = null)
    {
        $this->addMap(RequestMethod::DELETE, $module, $controller, $acl);
    }

    /**
     * Add mapping for OPTIONS method
     *
     * @param string $module
     * @param string $controller
     * @param String $acl
     */
    public function options($module, $controller, $acl = null)
    {
        $this->addMap(RequestMethod::OPTIONS, $module, $controller, $acl);
    }

    /**
     * Set Crud
     *
     * @param AbstractCrud $crud
     */
    public function setCrud($crud)
    {
        $this->crud = $crud;
    }

    /**
     * Get crud instance
     *
     * @return AbstractCrud
     * @throws ControllerException
     */
    public function getCrud()
    {
        if (!$this->crud) {
            throw new ControllerException("`Crud` class is not exists or not initialized");
        }
        return $this->crud;
    }

    /**
     * Return primary key
     *
     * @return array
     */
    public function getPrimaryKey()
    {
        if (is_null($this->primary)) {
            $primary = $this->getCrud()->getPrimaryKey();
            $this->primary = array_intersect_key($this->data, array_flip($primary));
        }
        return $this->primary;
    }

    /**
     * Run REST controller
     * @return mixed
     * @throws ForbiddenException
     * @throws NotImplementedException
     */
    public function run()
    {
        // check implementation
        if (!isset($this->map[$this->method])) {
            throw new NotImplementedException;
        }

        $map = $this->map[$this->method];

        // check permissions
        if (isset($map['acl'])) {
            if (!Acl::isAllowed($this->module, $map['acl'])) {
                throw new ForbiddenException;
            }
        }

        // dispatch controller
        return Application::getInstance()->dispatch(
            $map['module'],
            $map['controller'],
            [
                'crud' => $this->getCrud(),
                'primary' => $this->getPrimaryKey(),
                'data' => $this->data
            ]
        );
    }
}
