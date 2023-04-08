<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Db;

abstract class AbstractRow
{
    use Traits\TableProperty;
    use Traits\RowRelations;

    /**
     * This is set to a copy of $data when the data is fetched from
     * a database, specified as a new tuple in the constructor, or
     * when dirty data is posted to the database with save().
     *
     * @var array
     */
    private array $cleanRowData = [];

    private bool $editRowFlag = false;

    protected function setEditFlag(): void
    {
        if (!$this->editRowFlag) {
            $this->editRowFlag = true;
            $this->cleanRowData = $this->toArray();
        }
    }
    protected function resetEditFlag(): void
    {
        $this->editRowFlag = false;
        $this->cleanRowData = [];
    }


}
