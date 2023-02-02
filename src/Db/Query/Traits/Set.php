<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Db\Query\Traits;

use Bluz\Db\Query\Insert;
use Bluz\Db\Query\Update;
use Bluz\Proxy\Db;
use PDO;

/**
 * Set Trait
 *
 * Required for:
 *  - Insert Builder
 *  - Update Builder
 *
 * @package  Bluz\Db\Query\Traits
 * @author   Anton Shevchuk
 */
trait Set
{
    /**
     * <code>
     * [
     *     '`firstName` = ?',
     *     '`lastName` = ?'
     * ]
     * </code>
     *
     * @var array
     */
    protected array $set = [];

    /**
     * Set key-value pair
     *
     * Sets a new value for a column in a insert/update query
     * <code>
     *     $ub = new UpdateBuilder();
     *     $ub
     *         ->update('users')
     *         ->set('password', md5('password'))
     *         ->where('id = ?');
     * </code>
     *
     * @param string $key The column to set
     * @param string|integer $value The value, expression, placeholder, etc
     * @param int $type The type of value on of PDO::PARAM_* params
     *
     * @return Update|Insert
     */
    public function set(string $key, int|string $value, int $type = PDO::PARAM_STR): Insert|Update
    {
        $this->setParam(null, $value, $type);
        $this->set[] = Db::quoteIdentifier($key) . ' = ?';
        return $this;
    }

    /**
     * Set data from array
     *
     * <code>
     *     $ub = new UpdateBuilder();
     *     $ub
     *         ->update('users')
     *         ->setArray([
     *              'password' => md5('password')
     *              'updated' => date('Y-m-d H:i:s')
     *          ])
     *         ->where('u.id = ?');
     * </code>
     *
     * @param array $data
     *
     * @return Insert|Update
     */
    public function setArray(array $data): Insert|Update
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
        return $this;
    }

    /**
     * Prepare string to apply it inside SQL query
     *
     * @return string
     */
    protected function prepareSet(): string
    {
        return ' SET ' . implode(', ', $this->set);
    }
}
