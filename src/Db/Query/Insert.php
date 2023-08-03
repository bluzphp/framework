<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Db\Query;

use Bluz\Proxy\Db;

/**
 * Builder of INSERT queries
 *
 * @package Bluz\Db\Query
 */
class Insert extends AbstractBuilder
{
    use Traits\Set;

    /**
     * @var string Table name
     */
    protected string $table;

    /**
     * @inheritDoc
     *
     * @param null $sequence
     *
     * @return : array|int|string
     */
    public function execute($sequence = null): array|int|string
    {
        $result = Db::query($this->getSql(), $this->params, $this->types);
        if ($result) {
            return Db::handler()->lastInsertId($sequence);
        }
        return $result;
    }

    /**
     * @inheritDoc
     *
     * @return string
     */
    public function getSql(): string
    {
        return 'INSERT INTO '
            . Db::quoteIdentifier($this->table)
            . $this->prepareSet();
    }

    /**
     * Turns the query being built into an insert query that inserts into
     * a certain table
     *
     * Example
     * <code>
     *     $ib = new InsertBuilder();
     *     $ib
     *         ->insert('users')
     *         ->set('name', 'username')
     *         ->set('password', md5('password'));
     * </code>
     *
     * @param string $table The table into which the rows should be inserted
     *
     * @return Insert instance
     */
    public function insert(string $table): Insert
    {
        $this->table = $table;
        return $this;
    }
}
