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
 * Builder of UPDATE queries
 *
 * @package Bluz\Db\Query
 */
class Update extends AbstractBuilder
{
    use Traits\Set;
    use Traits\Where;
    use Traits\Limit;

    /**
     * @var string Table name
     */
    protected string $table;

    /**
     * @inheritDoc
     */
    public function getSql(): string
    {
        return 'UPDATE '
            . Db::quoteIdentifier($this->table)
            . $this->prepareSet()
            . $this->prepareWhere()
            . $this->prepareLimit();
    }

    /**
     * Turns the query being built into a bulk update query that ranges over
     * a certain table
     *
     * Example
     * <code>
     *     $ub = new UpdateBuilder();
     *     $ub
     *         ->update('users')
     *         ->set('password', md5('password'))
     *         ->where('id = ?');
     * </code>
     *
     * @param string $table the table whose rows are subject to the update
     *
     * @return Update instance
     */
    public function update(string $table): Update
    {
        $this->table = $table;
        return $this;
    }
}
