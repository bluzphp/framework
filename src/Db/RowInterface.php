<?php
/**
 * @namespace
 */

namespace Bluz\Db;

/**
 * RowInterface
 *
 * @package  Bluz\Db
 * @author   Anton Shevchuk
 */
interface RowInterface
{
    /**
     * Create Row instance
     *
     * @param array $data
     */
    public function __construct(array $data = []);

    /**
     * Returns the column/value data as an array
     *
     * @return array
     */
    public function toArray(): array;

    /**
     * Sets all data in the row from an array
     *
     * @param  array $data
     *
     * @return void
     */
    public function setFromArray(array $data): void;

    /**
     * Saves the properties to the database.
     *
     * This performs an intelligent insert/update, and reloads the
     * properties with fresh data from the table on success.
     *
     * @return mixed The primary key value(s), as an associative array if the
     *               key is compound, or a scalar if the key is single-column
     */
    public function save();

    /**
     * Delete existing row
     *
     * @return bool Removed or not
     */
    public function delete(): bool;

    /**
     * Refreshes properties from the database
     *
     * @return void
     */
    public function refresh(): void;
}
