<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Db\Query;

/**
 * Builder of SELECT queries
 *
 * @package Bluz\Db\Query
 */
class Delete extends AbstractBuilder
{
    use Traits\From;
    use Traits\Where;
    use Traits\Limit;

    /**
     * {@inheritdoc}
     */
    public function getSql()
    {
        $query = "DELETE FROM " . $this->sqlParts['from']['table']
            . ($this->sqlParts['where'] !== null ? " WHERE " . ((string) $this->sqlParts['where']) : "")
            . ($this->limit ? " LIMIT ". $this->limit : "")
        ;

        return $query;
    }

    /**
     * Turns the query being built into a bulk delete query that ranges over
     * a certain table
     *
     * Example
     *     $db = new DeleteBuilder();
     *     $db
     *         ->delete('users')
     *         ->where('id = ?');
     *
     * @param string $table The table whose rows are subject to the update
     * @return self instance.
     */
    public function delete($table)
    {
        $table = $this->getAdapter()->quoteIdentifier($table);
        return $this->addQueryPart('from', array('table' => $table));
    }
}
