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
namespace Bluz\Proxy;

use Bluz\Db\Db as Instance;
use Bluz\Db\Query;

/**
 * Proxy to Db
 *
 * Example of usage
 *     use Bluz\Proxy\Db;
 *
 *     Db::fetchAll('SELECT * FROM users');
 *
 * @package  Bluz\Proxy
 *
 * @method   static Instance getInstance()
 *
 * @method   static \PDO handler()
 * @see      Bluz\Db\Db::handler()
 *
 * @method   static string quote($value)
 * @see      Bluz\Db\Db::quote()
 *
 * @method   static string quoteIdentifier($identifier)
 * @see      Bluz\Db\Db::quoteIdentifier()
 *
 * @method   static integer query($sql, $params = array(), $types = array())
 * @see      Bluz\Db\Db::query()
 *
 * @method   static Query\Select select($select)
 * @see      Bluz\Db\Db::select()
 *
 * @method   static Query\Insert insert($table)
 * @see      Bluz\Db\Db::insert()
 *
 * @method   static Query\Update update($table)
 * @see      Bluz\Db\Db::update()
 *
 * @method   static Query\Delete delete($table)
 * @see      Bluz\Db\Db::delete()
 *
 * @method   static string fetchOne($sql, $params = [])
 * @see      Bluz\Db\Db::fetchOne()
 *
 * @method   static array fetchRow($sql, $params = [])
 * @see      Bluz\Db\Db::fetchRow()
 *
 * @method   static array fetchAll($sql, $params = [])
 * @see      Bluz\Db\Db::fetchAll()
 *
 * @method   static array fetchColumn($sql, $params = [])
 * @see      Bluz\Db\Db::fetchColumn()
 *
 * @method   static array fetchGroup($sql, $params = [])
 * @see      Bluz\Db\Db::fetchGroup()
 *
 * @method   static array fetchColumnGroup($sql, $params = [])
 * @see      Bluz\Db\Db::fetchColumnGroup()
 *
 * @method   static array fetchPairs($sql, $params = [])
 * @see      Bluz\Db\Db::fetchPairs()
 *
 * @method   static array fetchObject($sql, $params = [], $object = "stdClass")
 * @see      Bluz\Db\Db::fetchObject()
 *
 * @method   static array fetchObjects($sql, $params = [], $object = null)
 * @see      Bluz\Db\Db::fetchObjects()
 *
 * @method   static array fetchRelations($sql, $params = [])
 * @see      Bluz\Db\Db::fetchRelations()
 *
 * @method   static bool transaction($process)
 * @see      Bluz\Db\Db::transaction()
 *
 * @method   static void disconnect()
 * @see      Bluz\Db\Db::disconnect()
 *
 * @author   Anton Shevchuk
 * @created  26.09.2014 16:46
 */
class Db extends AbstractProxy
{
    /**
     * Init instance
     *
     * @return Instance
     */
    protected static function initInstance()
    {
        $instance = new Instance();
        $instance->setOptions(Config::getData('db'));
        return $instance;
    }
}
