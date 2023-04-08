<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Db;

use Bluz\Container;
use Bluz\Db\Exception\DbException;
use Bluz\Db\Exception\InvalidPrimaryKeyException;
use Bluz\Db\Exception\TableNotFoundException;

/**
 * Db Table Row
 *
 * Example of Users\Row
 * <code>
 *     namespace Application\Users;
 *     class Row extends \Bluz\Db\Row
 *     {
 *        public function beforeInsert(): void
 *        {
 *            $this->created = gmdate('Y-m-d H:i:s');
 *        }
 *
 *        public function beforeUpdate(): void
 *        {
 *            $this->updated = gmdate('Y-m-d H:i:s');
 *        }
 *     }
 *
 *     $userRow = new \Application\Users\Row();
 *     $userRow -> login = 'username';
 *     $userRow -> save();
 * </code>
 *
 * @package  Bluz\Db
 * @author   Anton Shevchuk
 * @link     https://github.com/bluzphp/framework/wiki/Db-Row
 */
abstract class Row extends AbstractRow implements RowInterface //, \JsonSerializable //, \ArrayAccess
{
//    use Container\Container;
//    use Container\ArrayAccess;
//    use Container\JsonSerialize;
//    use Container\MagicAccess;

    /**
     * Create Row instance
     *
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        // not clean data, but not modified
        if (count($data)) {
            $this->setFromArray($data);
        }
        $this->afterRead();
    }


    /**
     * Returns the column/value data as an array
     *
     * @return array
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }

    /**
     * Sets all data in the row from an array
     *
     * @param array $data
     *
     * @return void
     */
    public function setFromArray(array $data): void
    {
        $this->setEditFlag();
        foreach ($data as $key => $value) {
            $this->__set($key, $value);
        }
    }

    public function __get($key)
    {
        if (property_exists($this, $key)) {
            return $this->{$key};
        }
        return null;
    }

    public function __set($key, $value): void
    {
        $this->setEditFlag();
        if (property_exists($this, $key)) {
            $this->{$key} = $value;
        }
    }

    public function __isset($key): bool
    {
        return property_exists($this, $key);
    }

    public function __unset(string $key): void
    {
        unset($this->{$key});
    }

    /**
     * Cast to string as class name
     *
     * @return string
     */
    public function __toString()
    {
        return static::class;
    }

    /**
     * Validate input data
     *
     * @param array $data
     *
     * @return bool
     */
    public function validate($data): bool
    {
        return true;
    }

    /**
     * Assert input data
     *
     * @param array $data
     *
     * @return void
     */
    public function assert($data): void
    {
        return;
    }

    /**
     * Saves the properties to the database.
     *
     * This performs an intelligent insert/update, and reloads the
     * properties with fresh data from the table on success.
     *
     * @return mixed The primary key value(s), as an associative array if the
     *               key is compound, or a scalar if the key is single-column
     * @throws DbException
     * @throws InvalidPrimaryKeyException
     * @throws TableNotFoundException
     */
    public function save(): mixed
    {
        $this->beforeSave();
        /**
         * If the primary key is empty, this is an INSERT of a new row.
         * Otherwise, check primary key updated or not, if it changed - INSERT
         * otherwise UPDATE
         */
        if (!count(array_filter($this->getPrimaryKey()))) {
            $result = $this->doInsert();
        } elseif (count(array_diff_assoc($this->getPrimaryKey(), $this->clean))) {
            $result = $this->doInsert();
        } else {
            $result = $this->doUpdate();
        }
        $this->afterSave();
        return $result;
    }

    /**
     * Insert row to Db
     *
     * @return mixed The primary key value(s), as an associative array if the
     *               key is compound, or a scalar if the key is single-column
     * @throws InvalidPrimaryKeyException
     * @throws TableNotFoundException
     */
    protected function doInsert(): mixed
    {
        /**
         * Run pre-INSERT logic
         */
        $this->beforeInsert();

        $data = $this->toArray();
        /**
         * Execute validator logic
         * Can throw ValidatorException
         */
        $this->assert($data);

        $table = $this->getTable();

        /**
         * Execute the INSERT (this may throw an exception)
         */
        $primaryKey = $table::insert($data);

        /**
         * Normalize the result to an array indexed by primary key column(s)
         */
        $tempPrimaryKey = $table->getPrimaryKey();
        $newPrimaryKey = [current($tempPrimaryKey) => $primaryKey];

        /**
         * Save the new primary key value in object. The primary key may have
         * been generated by a sequence or auto-increment mechanism, and this
         * merges should be done before the afterInsert() method is run, so the
         * new values are available for logging, etc.
         */
        $this->setFromArray($newPrimaryKey);

        /**
         * Run post-INSERT logic
         */
        $this->afterInsert();

        /**
         * Update the "clean" to reflect that the data has been inserted.
         */
        $this->clean = $this->toArray();

        return $newPrimaryKey;
    }

    /**
     * Update row
     *
     * @return integer The number of rows updated
     * @throws InvalidPrimaryKeyException
     * @throws TableNotFoundException
     * @throws DbException
     */
    protected function doUpdate(): int
    {
        /**
         * Run pre-UPDATE logic
         */
        $this->beforeUpdate();

        $data = $this->toArray();

        /**
         * Execute validator logic
         * Can throw ValidatorException
         */
        $this->assert($data);

        $primaryKey = $this->getPrimaryKey();

        /**
         * Compare the data to the modified fields array to discover
         * which columns have been changed.
         */
        $diffData = array_diff_assoc($data, $this->clean);

        /* @var Table $table */
        $table = $this->getTable();

        $diffData = $table::filterColumns($diffData);

        /**
         * Execute the UPDATE (this may throw an exception)
         * Do this only if data values were changed.
         * Use the $diffData variable, so the UPDATE statement
         * includes SET terms only for data values that changed.
         */
        $result = 0;
        if (count($diffData) > 0) {
            $result = $table::update($diffData, $primaryKey);
        }

        /**
         * Run post-UPDATE logic.  Do this before the _refresh()
         * so the _afterUpdate() function can tell the difference
         * between changed data and clean (pre-changed) data.
         */
        $this->afterUpdate();

        /**
         * Refresh the data just in case triggers in the RDBMS changed
         * any columns. Also, this resets the "clean".
         */
        $this->resetEditFlag();

        return $result;
    }

    /**
     * Delete existing row
     *
     * @return bool Removed or not
     * @throws InvalidPrimaryKeyException
     * @throws TableNotFoundException
     */
    public function delete(): bool
    {
        /**
         * Execute pre-DELETE logic
         */
        $this->beforeDelete();

        $primaryKey = $this->getPrimaryKey();

        /**
         * Execute the DELETE (this may throw an exception)
         */
        $table = $this->getTable();
        $result = $table::delete($primaryKey);

        /**
         * Execute post-DELETE logic
         */
        $this->afterDelete();

        /**
         * Reset all fields to null to indicate that the row is not there
         */
        //$this->resetArray();

        return $result > 0;
    }

    /**
     * Retrieves an associative array of primary keys, if it exists
     *
     * @return array
     * @throws InvalidPrimaryKeyException
     * @throws TableNotFoundException
     */
    protected function getPrimaryKey(): array
    {
        $primary = array_flip($this->getTable()->getPrimaryKey());

        return array_intersect_key($this->toArray(), $primary);
    }

    /**
     * Refreshes properties from the database
     *
     * @return void
     */
    public function refresh(): void
    {
        $this->setFromArray($this->clean);
        $this->afterRead();
    }

    /**
     * After read data from Db
     *
     * @return void
     */
    protected function afterRead(): void
    {
    }

    /**
     * Allows pre-insert and pre-update logic to be applied to row.
     * Subclasses may override this method
     *
     * @return void
     */
    protected function beforeSave(): void
    {
    }

    /**
     * Allows post-insert and post-update logic to be applied to row.
     * Subclasses may override this method
     *
     * @return void
     */
    protected function afterSave(): void
    {
    }

    /**
     * Allows pre-insert logic to be applied to row.
     * Subclasses may override this method
     *
     * @return void
     */
    protected function beforeInsert(): void
    {
    }

    /**
     * Allows post-insert logic to be applied to row.
     * Subclasses may override this method
     *
     * @return void
     */
    protected function afterInsert(): void
    {
    }

    /**
     * Allows pre-update logic to be applied to row.
     * Subclasses may override this method
     *
     * @return void
     */
    protected function beforeUpdate(): void
    {
    }

    /**
     * Allows post-update logic to be applied to row.
     * Subclasses may override this method
     *
     * @return void
     */
    protected function afterUpdate(): void
    {
    }

    /**
     * Allows pre-delete logic to be applied to row.
     * Subclasses may override this method
     *
     * @return void
     */
    protected function beforeDelete(): void
    {
    }

    /**
     * Allows post-delete logic to be applied to row.
     * Subclasses may override this method
     *
     * @return void
     */
    protected function afterDelete(): void
    {
    }
}
