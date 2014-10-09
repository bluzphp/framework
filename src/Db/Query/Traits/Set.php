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
 * Set Trait, required for:
 *  - Insert Builder
 *  - Update  Builder
 *
 * @package Bluz\Db\Query\Traits
 *
 * @method Insert|Update addQueryPart(string $sqlPartName, mixed $sqlPart, $append = false)
 * @method Insert|Update setParameter(string $key, mixed $value, $type = \PDO::PARAM_STR)
 *
 * @author   Anton Shevchuk
 * @created  17.06.13 10:00
 */
trait Set
{
    /**
     * Set key-value pair
     *
     * Sets a new value for a column in a insert/update query
     *     $ub = new UpdateBuilder();
     *     $ub
     *         ->update('users')
     *         ->set('password', md5('password'))
     *         ->where('id = ?');
     *
     * @param string $key The column to set
     * @param string $value The value, expression, placeholder, etc
     * @return Insert|Update
     */
    public function set($key, $value)
    {
        $this->setParameter(null, $value, \PDO::PARAM_STR);
        $key = Db::quoteIdentifier($key);
        return $this->addQueryPart('set', $key .' = ?', true);
    }

    /**
     * setArray
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
     * @return Insert|Update
     */
    public function setArray(array $data)
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
        return $this;
    }
}
