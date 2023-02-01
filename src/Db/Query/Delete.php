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
 * Builder of DELETE queries
 *
 * @package Bluz\Db\Query
 */
class Delete extends AbstractBuilder
{
    use Traits\Where;
    use Traits\Order;
    use Traits\Limit;

    /**
     * @var string Table name
     */
    protected $table;

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getSql(): string
    {
        return 'DELETE FROM '
            . Db::quoteIdentifier($this->table)
            . $this->prepareWhere()
            . $this->prepareLimit();
    }

    /**
     * Turns the query being built into a bulk delete query that ranges over
     * a certain table
     *
     * Example
     * <code>
     *     $db = new DeleteBuilder();
     *     $db
     *         ->delete('users')
     *         ->where('id = ?');
     * </code>
     *
     * @param string $table The table whose rows are subject to the update
     *
     * @return Delete instance
     */
    public function delete($table): Delete
    {
        $this->table = $table;
        return $this;
    }

    /**
     * Prepare string to apply limit inside SQL query
     *
     * @return string
     */
    protected function prepareLimit(): string
    {
        return $this->limit ? ' LIMIT ' . $this->limit : '';
    }
}
