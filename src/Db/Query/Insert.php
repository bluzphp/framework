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
     * {@inheritdoc}
     *
     * @param null $sequence
     * @return mixed
     */
    public function execute($sequence = null)
    {
        $result = Db::query($this->getSQL(), $this->params, $this->types);
        if ($result) {
            return Db::handler()->lastInsertId($sequence);
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getSql()
    {
        $query = "INSERT"
            . " INTO " . $this->sqlParts['from']['table']
            . " SET " . join(", ", $this->sqlParts['set']);

        return $query;
    }

    /**
     * Turns the query being built into an insert query that inserts into
     * a certain table
     *
     * Example
     *     $ib = new InsertBuilder();
     *     $ib
     *         ->insert('users')
     *         ->set('name', 'username')
     *         ->set('password', md5('password'));
     *
     * @param string $table The table into which the rows should be inserted
     * @return Insert instance
     */
    public function insert($table)
    {
        return $this->setFromQueryPart($table);
    }
}
