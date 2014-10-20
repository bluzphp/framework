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
            $this->primary = array_shift($params);
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
     * @throws NotImplementedException
     * @throws NotFoundException
     * @throws BadRequestException
     * @return mixed
     */
    public function __invoke()
    {
        // everyone method can return:
        // >> 401 Unauthorized - if authorization is required
        // >> 403 Forbidden - if user don't have permissions
        // >> 501 Not Implemented - if something not exists

        // GET    /module/rest/   -> 200 // return collection or
        //                        -> 206 // return part of collection
        // GET    /module/rest/id -> 200 // return one item or
        //                        -> 404 // not found
        // POST   /module/rest/   -> 201 // item created or
        //                        -> 400 // bad request, validation error
        // POST   /module/rest/id -> 501 // error, not used in REST
        // PATCH  /module/rest/
        // PUT    /module/rest/   -> 200 // all items was updated or
        //                        -> 207 // multi-status ?
        // PATCH  /module/rest/id
        // PUT    /module/rest/id -> 200 // item was updated or
        //                        -> 304 // item not modified or
        //                        -> 400 // bad request, validation error or
        //                        -> 404 // not found
        // DELETE /module/rest/   -> 204 // all items was deleted or
        //                        -> 207 // multi-status ?
        // DELETE /module/rest/id -> 204 // item was deleted
        //                        -> 404 // not found
        switch ($this->method) {
            case Request::METHOD_GET:
                if ($this->primary) {
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
                // break
            case Request::METHOD_POST:
                if ($this->primary) {
                    // POST + ID is incorrect behaviour
                    throw new NotImplementedException();
                }
                if (!sizeof($this->data)) {
                    // can't create empty entity
                    throw new BadRequestException();
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
            case Request::METHOD_PATCH:
            case Request::METHOD_PUT:
                if (!sizeof($this->data)) {
                    // data not found
                    throw new BadRequestException();
                }

                try {
                    if ($this->primary) {
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
            case Request::METHOD_DELETE:
                if ($this->primary) {
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
            default:
                throw new NotImplementedException();
        }
    }
}
