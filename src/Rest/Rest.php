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
namespace Bluz\Rest;

use Bluz\Application\Application;
use Bluz\Application\Exception\ForbiddenException;
use Bluz\Application\Exception\NotImplementedException;
use Bluz\Crud\AbstractCrud;
use Bluz\Proxy\Acl;
use Bluz\Proxy\Request;
use Bluz\Proxy\Response;
use Bluz\Proxy\Router;

/**
 * Rest
 *
 * @package  Bluz\Rest
 * @author   Anton Shevchuk
 */
class Rest
{
    /**
     * @var string HTTP Method
     */
    protected $method = Request::METHOD_GET;

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
    protected $params = array();

    /**
     * @var array query data
     */
    protected $data = array();

    /**
     * @var AbstractCrud instance of CRUD
     */
    protected $crud;

    /**
     * METHOD => [
     *     'module' => 'module',
     *     'controller' => 'controller',
     *     'acl' => 'privilege'
     * ]
     * 
     * @var array
     */
    protected $map = array();

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
        $this->map[strtoupper($method)] = array(
            'module' => $module,
            'controller' => $controller,
            'acl' => $acl
        );
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
     * process
     *
     * @return void
     */
    public function process()
    {
        // HTTP method
        $method = Request::getMethod();
        $this->method = strtoupper($method);

        // get path
        // %module% / %controller% / %id% / %relation% / %id%
        $path = Router::getCleanUri();

        $params = explode('/', rtrim($path, '/'));

        $this->params = $params;

        // module
        $this->module = array_shift($params);

        // controller
        $this->controller = array_shift($params);

        if (sizeof($params)) {
            $this->primary = explode('-', array_shift($params));
        }

        if (sizeof($params)) {
            $this->relation = array_shift($params);
        }
        if (sizeof($params)) {
            $this->relationId = array_shift($params);
        }

        $data = Request::getParams();

        unset($data['_method'], $data['_module'], $data['_controller']);

        $this->data = $data;
    }

    /**
     * Run REST controller
     * @return mixed
     * @throws ForbiddenException
     * @throws NotImplementedException
     */
    public function run()
    {
        // OPTIONS
        if ('OPTIONS' == $this->method) {
            Response::setHeader('Allow', join(',', array_keys($this->map)));
            return null;
        }
        
        
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
                'crud' => $this->crud,
                'primary' => $this->primary,
                'data' => $this->data
            ]
            );
    }
}
