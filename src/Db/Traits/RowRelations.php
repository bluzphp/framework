<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Db\Traits;

use Bluz\Db\Relations;
use Bluz\Db\Row;
use Bluz\Db\Exception\RelationNotFoundException;
use Bluz\Db\Exception\TableNotFoundException;
use Bluz\Db\RowInterface;

/**
 * RowRelations
 *
 * @package  Bluz\Db\Traits
 * @author   Anton Shevchuk
 */
trait RowRelations
{
    /**
     * @var array relations rows
     */
    private array $relations = [];

    /**
     * Set relation
     *
     * @param Row $row
     *
     * @return void
     * @throws TableNotFoundException
     */
    public function setRelation(Row $row): void
    {
        $modelName = $row->getTable()->getModel();
        $this->relations[$modelName] = [$row];
    }

    /**
     * Get relation by model name
     *
     * @param string $modelName
     *
     * @return RowInterface|null
     * @throws RelationNotFoundException
     * @throws TableNotFoundException
     */
    public function getRelation($modelName): ?RowInterface
    {
        $relations = $this->getRelations($modelName);
        return empty($relations) ? null : current($relations);
    }

    /**
     * Get relations by model name
     *
     * @param string $modelName
     *
     * @return RowInterface[]
     * @throws RelationNotFoundException
     * @throws TableNotFoundException
     */
    public function getRelations($modelName): array
    {
        $this->relations[$modelName] ??= Relations::findRelation($this, $modelName);

        return $this->relations[$modelName];
    }
}
