<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Db\Traits;

use Bluz\Db\Exception\TableNotFoundException;
use Bluz\Db\TableInterface;

/**
 * TableProperty
 *
 * @package  Bluz\Db\Traits
 * @author   Anton Shevchuk
 */
trait TableProperty
{
    /**
     * @var TableInterface|null instance
     */
    protected ?TableInterface $table = null;

    /**
     * Setup Table instance
     *
     * @param TableInterface $table
     *
     * @return void
     */
    public function setTable(TableInterface $table): void
    {
        $this->table = $table;
    }

    /**
     * Return table instance for manipulation
     *
     * @return TableInterface
     * @throws TableNotFoundException
     */
    public function getTable(): TableInterface
    {
        if (!$this->table) {
            $this->initTable();
        }
        return $this->table;
    }

    /**
     * Init table instance for manipulation
     *
     * @return void
     * @throws TableNotFoundException
     */
    protected function initTable(): void
    {
        $tableClass = class_namespace(static::class) . '\\Table';

        // check class initialization
        if (!class_exists($tableClass) || !is_subclass_of($tableClass, TableInterface::class)) {
            throw new TableNotFoundException('`Table` class is not exists or not initialized');
        }

        /**
         * @var TableInterface $tableClass
         */
        $this->setTable($tableClass::getInstance());
    }
}
