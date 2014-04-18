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
class Update extends AbstractBuilder
{
    use Traits\Set;
    use Traits\Where;
    use Traits\Limit;

    /**
     * {@inheritdoc}
     */
    public function getSql()
    {
        $query = "UPDATE " . $this->sqlParts['from']['table']
            . " SET " . join(", ", $this->sqlParts['set'])
            . ($this->sqlParts['where'] !== null ? " WHERE " . ((string) $this->sqlParts['where']) : "")
            . ($this->limit ? " LIMIT ". $this->limit : "")
        ;

        return $query;
    }

    /**
     * Turns the query being built into a bulk update query that ranges over
     * a certain table
     *
     * Example
     *     $ub = new UpdateBuilder();
     *     $ub
     *         ->update('users')
     *         ->set('password', md5('password'))
     *         ->where('id = ?');
     *
     * @param string $table The table whose rows are subject to the update
     * @return self instance.
     */
    public function update($table)
    {
        $table = $this->getAdapter()->quoteIdentifier($table);
        return $this->addQueryPart('from', array('table' => $table));
    }
}
