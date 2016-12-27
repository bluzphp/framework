<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Db\Query;

/**
 * Builder of UPDATE queries
 *
 * @package Bluz\Db\Query
 */
class Update extends AbstractBuilder
{
    use Traits\Set;
    use Traits\Where;
    use Traits\Order;
    use Traits\Limit;

    /**
     * {@inheritdoc}
     *
     * @return string
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
     * <code>
     *     $ub = new UpdateBuilder();
     *     $ub
     *         ->update('users')
     *         ->set('password', md5('password'))
     *         ->where('id = ?');
     * </code>
     *
     * @param  string $table the table whose rows are subject to the update
     * @return Update instance
     */
    public function update($table)
    {
        return $this->setFromQueryPart($table);
    }
}
