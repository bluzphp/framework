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
use Bluz\Proxy\Messages;
use Bluz\Proxy\Request;
use Bluz\Validator\Exception\ValidatorException;

/**
 * Crud controller
 *
 * @package  Bluz\Controller
 * @author   Anton Shevchuk
 * @link     https://github.com/bluzphp/framework/wiki/Controller-Crud
 */
class Crud extends AbstractController
{
    /**
     * {@inheritdoc}
     *
     * @return mixed
     * @throws NotImplementedException
     * @throws NotFoundException
     * @throws BadRequestException
     */
    public function __invoke()
    {
        $primary = $this->getPrimaryKey();

        // switch by method
        switch ($this->method) {
            case Request::METHOD_GET:
                $result = [
                    'row' => $this->readOne($primary),
                    'method' => empty($primary)?Request::METHOD_POST:Request::METHOD_PUT
                ];
                break;
            case Request::METHOD_POST:
                try {
                    // Result is Primary Key(s)
                    $result = $this->createOne($this->data);
                    if (!Request::isXmlHttpRequest()) {
                        $result = [
                            'row'    => $this->readOne($result),
                            'method' => Request::METHOD_PUT
                        ];
                    }
                } catch (ValidatorException $e) {
                    $row = $this->readOne(null);
                    $row->setFromArray($this->data);
                    $result = [
                        'row'    => $row,
                        'errors' => $e->getErrors(),
                        'method' => $this->getMethod()
                    ];
                }
                break;
            case Request::METHOD_PATCH:
            case Request::METHOD_PUT:
                try {
                    // Result is numbers of affected rows
                    $result = $this->updateOne($primary, $this->data);
                    if (!Request::isXmlHttpRequest()) {
                        $result = [
                            'row'    => $this->readOne($primary),
                            'method' => $this->getMethod()
                        ];
                    }
                } catch (ValidatorException $e) {
                    $row = $this->readOne($primary);
                    $row->setFromArray($this->data);
                    $result = [
                        'row'    => $row,
                        'errors' => $e->getErrors(),
                        'method' => $this->getMethod()
                    ];
                }
                break;
            case Request::METHOD_DELETE:
                // Result is numbers of affected rows
                $result = $this->deleteOne($primary);
                break;
            default:
                throw new NotImplementedException();
        }
        return $result;
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
     * {@inheritdoc}
     *
     * @param  array $data
     * @return mixed
     * @throws \Bluz\Application\Exception\ApplicationException
     * @throws \Bluz\Application\Exception\NotImplementedException
     */
    public function createOne($data)
    {
        $result = parent::createOne($data);

        Messages::addSuccess("Record was created");

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @param  mixed $id
     * @param  array $data
     * @return integer
     * @throws \Bluz\Application\Exception\ApplicationException
     * @throws \Bluz\Application\Exception\NotImplementedException
     */
    public function updateOne($id, $data)
    {
        $result = parent::updateOne($id, $data);

        Messages::addSuccess("Record was updated");

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @param  mixed $primary
     * @return integer
     * @throws \Bluz\Application\Exception\ApplicationException
     * @throws \Bluz\Application\Exception\NotImplementedException
     */
    public function deleteOne($primary)
    {
        $result = parent::deleteOne($primary);

        Messages::addSuccess("Record was deleted");

        return $result;
    }
}
