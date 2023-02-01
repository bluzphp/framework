<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Crud;

use Bluz\Common\Instance;
use Bluz\Db\RowInterface;
use Bluz\Http\Exception\NotImplementedException;

/**
 * Crud
 *
 * @package  Bluz\Crud
 * @author   Anton Shevchuk
 * @link     https://github.com/bluzphp/framework/wiki/Crud
 */
abstract class AbstractCrud implements CrudInterface
{
    use Instance;

    /**
     * @var array Fields for action
     * @todo should be different for Create, Read and Update
     */
    protected array $fields = [];

    /**
     * Return primary key signature
     *
     * @return array
     */
    abstract public function getPrimaryKey(): array;

    /**
     * {@inheritdoc}
     *
     * @throws NotImplementedException
     */
    public function readOne($primary)
    {
        throw new NotImplementedException();
    }

    /**
     * {@inheritdoc}
     *
     * @throws NotImplementedException
     */
    public function readSet(int $offset = 0, int $limit = self::DEFAULT_LIMIT, array $params = [])
    {
        throw new NotImplementedException();
    }

    /**
     * {@inheritdoc}
     *
     * @throws NotImplementedException
     */
    public function createOne(array $data)
    {
        throw new NotImplementedException();
    }

    /**
     * {@inheritdoc}
     *
     * @throws NotImplementedException
     */
    public function createSet(array $data)
    {
        throw new NotImplementedException();
    }

    /**
     * {@inheritdoc}
     *
     * @throws NotImplementedException
     */
    public function updateOne($primary, array $data)
    {
        throw new NotImplementedException();
    }

    /**
     * {@inheritdoc}
     *
     * @throws NotImplementedException
     */
    public function updateSet(array $data)
    {
        throw new NotImplementedException();
    }

    /**
     * {@inheritdoc}
     *
     * @throws NotImplementedException
     */
    public function deleteOne($primary)
    {
        throw new NotImplementedException();
    }

    /**
     * {@inheritdoc}
     *
     * @throws NotImplementedException
     */
    public function deleteSet(array $data)
    {
        throw new NotImplementedException();
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param array $fields
     */
    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    /**
     * Filter input Fields
     *
     * @param array $data Request
     *
     * @return array
     */
    protected function filterData($data): array
    {
        if (empty($this->getFields())) {
            return $data;
        }
        return array_intersect_key($data, array_flip($this->getFields()));
    }

    /**
     * Filter output Row
     *
     * @param RowInterface $row from database
     *
     * @return RowInterface
     */
    protected function filterRow(RowInterface $row): RowInterface
    {
        if (empty($this->getFields())) {
            return $row;
        }
        $fields = array_keys($row->toArray());
        $toDelete = array_diff($fields, $this->getFields());

        foreach ($toDelete as $field) {
            unset($row->$field);
        }
        return $row;
    }
}
