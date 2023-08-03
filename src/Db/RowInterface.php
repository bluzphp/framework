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
interface RowInterface extends \JsonSerializable
{
    /**
     * Create Row instance
     *
     * @param array $data
     */
    public function __construct(array $data = []);

    /**
     * Sets all data in the row from an array
     *
     * @param array $data
     *
     * @return void
     */
    public function setFromArray(array $data): void;

    /**
     * Returns the column/value data as an array
     *
     * @return array
     */
    public function toArray(): array;
}
