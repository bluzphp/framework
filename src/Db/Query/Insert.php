<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Db\Query;

use Bluz\Db\Db;

/**
 * Builder of SELECT queries
 */
class Insert extends AbstractBuilder
{
    use Traits\Set;

    /**
     * {@inheritdoc}
     */
    public function execute($sequence = null)
    {
        $result = $this->getAdapter()->query($this->getSQL(), $this->params, $this->paramTypes);
        if ($result) {
            return $this->getAdapter()->handler()->lastInsertId($sequence);
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getSql()
    {
        $query = "INSERT INTO " . $this->sqlParts['from']['table']
            . " SET " . join(", ", $this->sqlParts['set']);

        return $query;
    }

    /**
     * Turns the query being built into an insert query that inserts into
     * a certain table
     *
     * <code>
     *
     *     $ib = new InsertBuilder();
     *     $ib
     *         ->insert('users')
     *         ->set('name', 'username')
     *         ->set('password', md5('password'));
     * </code>
     *
     * @param string $table The table into which the rows should be inserted
     * @return self instance
     */
    public function insert($table)
    {
        $table = $this->getAdapter()->quoteIdentifier($table);
        return $this->addQueryPart('from', array('table' => $table));
    }
}
