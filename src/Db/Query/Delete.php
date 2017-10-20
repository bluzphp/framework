<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Db\Query;

/**
 * Builder of DELETE queries
 *
 * @package Bluz\Db\Query
 */
class Delete extends AbstractBuilder
{
    use Traits\From;
    use Traits\Where;
    use Traits\Order;
    use Traits\Limit;

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getSql() : string
    {
        $query = 'DELETE FROM ' . $this->sqlParts['from']['table']
            . ($this->sqlParts['where'] !== null ? ' WHERE ' . ((string)$this->sqlParts['where']) : '')
            . ($this->limit ? ' LIMIT ' . $this->limit : '');

        return $query;
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
     * @param  string $table The table whose rows are subject to the update
     *
     * @return Delete instance
     */
    public function delete($table)
    {
        return $this->setFromQueryPart($table);
    }
}
