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
namespace Bluz\Db\Query\Traits;

use Bluz\Db\Query\Insert;
use Bluz\Db\Query\Update;
use Bluz\Proxy\Db;

/**
 * Set Trait
 *
 * Required for:
 *  - Insert Builder
 *  - Update Builder
 *
 * @package  Bluz\Db\Query\Traits
 * @author   Anton Shevchuk
 *
 * @method   Insert|Update addQueryPart(string $sqlPartName, mixed $sqlPart, $append = false)
 * @method   Insert|Update setParameter(string $key, mixed $value, $type = \PDO::PARAM_STR)
 */
trait Set
{
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
     * @param  string $key   The column to set
     * @param  string $value The value, expression, placeholder, etc
     * @return $this
     */
    public function set($key, $value)
    {
        $this->setParameter(null, $value, \PDO::PARAM_STR);
        $key = Db::quoteIdentifier($key);
        return $this->addQueryPart('set', $key .' = ?', true);
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
     * @param  array $data
     * @return $this
     */
    public function setArray(array $data)
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
        return $this;
    }
}
