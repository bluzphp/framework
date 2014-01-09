<?php
/**
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
use Bluz\Crud\AbstractCrud;
use Bluz\Crud\ValidationException;
use Bluz\Request\AbstractRequest;

/**
 * Crud
 *
 * @category Bluz
 * @package  Controller
 *
 * @author   Anton Shevchuk
 * @created  15.08.12 15:37
 */
class Crud extends AbstractController
{
    /**
     * @throws NotImplementedException
     * @throws NotFoundException
     * @throws BadRequestException
     * @return mixed
     */
    public function __invoke()
    {
        $primary = $this->getPrimaryKey();

        // switch by method
        switch ($this->method) {
            case AbstractRequest::METHOD_GET:
                $row = $this->readOne($primary);

                $result = ['row' => $row];
                if ($primary) {
                    // update form
                    $result['method'] = AbstractRequest::METHOD_PUT;
                } else {
                    // create form
                    $result['method'] = AbstractRequest::METHOD_POST;
                }
                return $result;
                break;
            case AbstractRequest::METHOD_POST:
                try {
                    $result = $this->createOne($this->data);
                    if (!app()->getRequest()->isXmlHttpRequest()) {
                        $row = $this->readOne($result);
                        $result = [
                            'row'    => $row,
                            'method' => AbstractRequest::METHOD_PUT
                        ];
                        return $result;
                    }
                } catch (ValidationException $e) {
                    $row = $this->readOne(null);
                    $row->setFromArray($this->data);
                    $result = [
                        'row'    => $row,
                        'errors' => $this->getCrud()->getErrors(),
                        'method' => $this->getMethod()
                    ];
                    return $result;
                }
                break;
            case AbstractRequest::METHOD_PATCH:
            case AbstractRequest::METHOD_PUT:
                try {
                    $this->updateOne($primary, $this->data);
                    if (!app()->getRequest()->isXmlHttpRequest()) {
                        $row = $this->readOne($primary);
                        $result = [
                            'row'    => $row,
                            'method' => $this->getMethod()
                        ];
                        return $result;
                    }
                } catch (ValidationException $e) {
                    $row = $this->readOne($primary);
                    $row->setFromArray($this->data);
                    $result = [
                        'row'    => $row,
                        'errors' => $this->getCrud()->getErrors(),
                        'method' => $this->getMethod()
                    ];
                    return $result;
                }
                break;
            case AbstractRequest::METHOD_DELETE:
                $this->deleteOne($primary);
                break;
            default:
                throw new NotImplementedException();
                break;
        }
    }

    /**
     * Return primary key
     *
     * @return array
     */
    public function getPrimaryKey()
    {
        if (!$this->primary) {
            $primary = $this->getCrud()->getPrimaryKey();
            $this->primary = array_intersect_key($this->data, array_flip($primary));
        }
        return $this->primary;
    }

    /**
     * {@inheritdoc}
     */
    public function createOne($data)
    {
        $result = $this->getCrud()->createOne($data);

        app()->getMessages()->addSuccess("Record was created");

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function updateOne($id, $data)
    {
        $result = $this->getCrud()->updateOne($id, $data);

        app()->getMessages()->addSuccess("Record was updated");

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteOne($primary)
    {
        $result = $this->getCrud()->deleteOne($primary);

        app()->getMessages()->addSuccess("Record was deleted");

        return $result;
    }
}
