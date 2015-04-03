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

use Bluz\Application\Exception\BadRequestException;
use Bluz\Application\Exception\NotFoundException;
use Bluz\Application\Exception\NotImplementedException;
use Bluz\Proxy\Response;
use Bluz\Proxy\Request;
use Bluz\Proxy\Router;
use Bluz\Validator\Exception\ValidatorException;

/**
 * Controller
 *
 * @package  Bluz\Controller
 * @link     https://github.com/bluzphp/framework/wiki/Controller-Rest
 *
 * @author   Anton Shevchuk
 * @created  27.09.13 15:32
 */
class Rest extends AbstractController
{
    /**
     * @var string Relation list
     */
    protected $relation;

    /**
     * @var string Relation Id
     */
    protected $relationId;

    /**
     * @var array Params of query
     */
    protected $params = array();

    /**
     * @var array Query data
     */
    protected $data = array();

    /**
     * Prepare request for processing
     */
    public function __construct()
    {
        parent::__construct();

        $params = Request::getRawParams();

        // %module% / %controller% / %id% / %relation% / %id%
        if (sizeof($params)) {
            $this->primary = explode('-', array_shift($params));
        }
        if (sizeof($params)) {
            $this->relation = array_shift($params);
        }
        if (sizeof($params)) {
            $this->relationId = array_shift($params);
        }
    }

    /**
     * {@inheritdoc}
     *
     * Everyone method can return:
     *    401 Unauthorized - if authorization is required
     *    403 Forbidden - if user don't have permissions
     *    501 Not Implemented - if something not exists
     *
     * Methods can return:
     *    HEAD   /module/rest/   -> 200 // return overview of collection
     *    HEAD   /module/rest/id -> 200 // return overview of item
     *                           -> 404 // not found
     *    GET    /module/rest/   -> 200 // return collection or
     *                           -> 206 // return part of collection
     *    GET    /module/rest/id -> 200 // return one item or
     *                           -> 404 // not found
     *    POST   /module/rest/   -> 201 // item created or
     *                           -> 400 // bad request, validation error
     *    POST   /module/rest/id -> 501 // error, not used in REST
     *    PATCH  /module/rest/
     *    PUT    /module/rest/   -> 200 // all items was updated or
     *                           -> 207 // multi-status ?
     *    PATCH  /module/rest/id
     *    PUT    /module/rest/id -> 200 // item was updated or
     *                           -> 304 // item not modified or
     *                           -> 400 // bad request, validation error or
     *                           -> 404 // not found
     *    DELETE /module/rest/   -> 204 // all items was deleted or
     *                           -> 207 // multi-status ?
     *    DELETE /module/rest/id -> 204 // item was deleted
     *                           -> 404 // not found
     *
     * @throws NotImplementedException
     * @throws NotFoundException
     * @throws BadRequestException
     * @return mixed
     */
    public function __invoke()
    {
        switch ($this->method) {
            case Request::METHOD_HEAD:
            case Request::METHOD_GET:
                return $this->methodGet();
                // break
            case Request::METHOD_POST:
                return $this->methodPost();
                // break
            case Request::METHOD_PATCH:
            case Request::METHOD_PUT:
                return $this->methodPut();
                // break
            case Request::METHOD_DELETE:
                return $this->methodDelete();
                // break
            case Request::METHOD_OPTIONS:
                return $this->methodOptions();
                // break
            default:
                throw new NotImplementedException();
        }
    }

    /**
     * Method HEAD and GET
     * @return mixed
     */
    public function methodGet()
    {
        if (!empty($this->primary)) {
            // @throws NotFoundException
            $result = $this->readOne($this->primary);
            return [$result];
        } else {
            // setup default offset and limit - safe way
            $offset = isset($this->params['offset'])?$this->params['offset']:0;
            $limit = isset($this->params['limit'])?$this->params['limit']:10;

            if ($range = Request::getHeader('Range')) {
                list(, $offset, $last) = preg_split('/[-=]/', $range);
                // for better compatibility
                $limit = $last - $offset;
            }
            return $this->readSet($offset, $limit, $this->params);
        }
    }

    /**
     * Method POST
     * @throws BadRequestException
     * @throws NotImplementedException
     * @return array|false
     */
    public function methodPost()
    {
        if (!empty($this->primary)) {
            // POST + ID is incorrect behaviour
            throw new NotImplementedException();
        }

        try {
            $result = $this->createOne($this->data);
            if (!$result) {
                // system can't create record with this data
                throw new BadRequestException();
            }

            if (is_array($result)) {
                $result = join('-', array_values($result));
            }

        } catch (ValidatorException $e) {
            Response::setStatusCode(400);
            return ['errors' => $e->getErrors()];
        }

        Response::setStatusCode(201);
        Response::setHeader(
            'Location',
            Router::getUrl(Request::getModule(), Request::getController()).'/'.$result
        );
        return false; // disable view
    }

    /**
     * Method PUT
     * @throws BadRequestException
     * @return array|false
     */
    public function methodPut()
    {
        if (!sizeof($this->data)) {
            // data not found
            throw new BadRequestException();
        }

        try {
            if (!empty($this->primary)) {
                // update one item
                $result = $this->updateOne($this->primary, $this->data);
            } else {
                // update collection
                $result = $this->updateSet($this->data);
            }
            // if $result === 0 it's means a update is not apply
            // or records not found
            if (0 === $result) {
                Response::setStatusCode(304);
            }
        } catch (ValidatorException $e) {
            Response::setStatusCode(400);
            return ['errors' => $e->getErrors()];
        }
        return false; // disable view
    }

    /**
     * Method DELETE
     * @throws BadRequestException
     * @return false
     */
    public function methodDelete()
    {
        if (!empty($this->primary)) {
            // delete one
            // @throws NotFoundException
            $this->deleteOne($this->primary);
        } else {
            // delete collection
            // @throws NotFoundException
            if (!sizeof($this->data)) {
                // data not exist
                throw new BadRequestException();
            }
            $this->deleteSet($this->data);
        }
        Response::setStatusCode(204);
        return false; // disable view
    }

    /**
     * Method OPTIONS
     * @return false
     */
    public function methodOptions()
    {
        $allow = $this->getMethods(sizeof($this->primary));
        Response::setHeader('Allow', join(',', $allow));
        return null; // no body
    }

    /**
     * Get allowed methods by CRUD
     * @param bool $primary
     * @return array
     */
    protected function getMethods($primary = false)
    {
        $methods = $this->getCrud()->getMethods();

        $allow = [Request::METHOD_HEAD, Request::METHOD_OPTIONS];

        if ($primary) {
            if (in_array('readOne', $methods)) {
                $allow[] = Request::METHOD_GET;
            }
            if (in_array('updateOne', $methods)) {
                $allow[] = Request::METHOD_PATCH;
                $allow[] = Request::METHOD_PUT;
            }
            if (in_array('deleteOne', $methods)) {
                $allow[] = Request::METHOD_DELETE;
            }
        } else {
            if (in_array('readSet', $methods)) {
                $allow[] = Request::METHOD_GET;
            }
            if (in_array('createOne', $methods)
                or in_array('createSet', $methods)) {
                $allow[] = Request::METHOD_POST;
            }
            if (in_array('updateSet', $methods)) {
                $allow[] = Request::METHOD_PATCH;
                $allow[] = Request::METHOD_PUT;
            }
            if (in_array('deleteSet', $methods)) {
                $allow[] = Request::METHOD_DELETE;
            }
        }
        return $allow;
    }
}
