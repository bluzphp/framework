<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Proxy;

use Bluz\Db\Db as Instance;
use Bluz\Db\Query;

/**
 * Proxy to Db
 *
 * Example of usage
 * <code>
 *     use Bluz\Proxy\Db;
 *
 *     Db::fetchAll('SELECT * FROM users');
 * </code>
 *
 * @package  Bluz\Proxy
 * @author   Anton Shevchuk
 *
 * @method   static Instance getInstance()
 *
 * @method   static mixed getOption($key, $section = null)
 * @see      Instance::getOption()
 *
 * @method   static \PDO handler()
 * @see      Instance::handler()
 *
 * @method   static string quote($value)
 * @see      Instance::quote()
 *
 * @method   static string quoteIdentifier($identifier)
 * @see      Instance::quoteIdentifier()
 *
 * @method   static integer query($sql, $params = [], $types = [])
 * @see      Instance::query()
 *
 * @method   static Query\Select select($select)
 * @see      Instance::select()
 *
 * @method   static Query\Insert insert($table)
 * @see      Instance::insert()
 *
 * @method   static Query\Update update($table)
 * @see      Instance::update()
 *
 * @method   static Query\Delete delete($table)
 * @see      Instance::delete()
 *
 * @method   static string fetchOne($sql, $params = [])
 * @see      Instance::fetchOne()
 *
 * @method   static array fetchRow($sql, $params = [])
 * @see      Instance::fetchRow()
 *
 * @method   static array fetchAll($sql, $params = [])
 * @see      Instance::fetchAll()
 *
 * @method   static array fetchColumn($sql, $params = [])
 * @see      Instance::fetchColumn()
 *
 * @method   static array fetchGroup($sql, $params = [], $object = null))
 * @see      Instance::fetchGroup()
 *
 * @method   static array fetchColumnGroup($sql, $params = [])
 * @see      Instance::fetchColumnGroup()
 *
 * @method   static array fetchPairs($sql, $params = [])
 * @see      Instance::fetchPairs()
 *
 * @method   static array fetchObject($sql, $params = [], $object = "stdClass")
 * @see      Instance::fetchObject()
 *
 * @method   static array fetchObjects($sql, $params = [], $object = null)
 * @see      Instance::fetchObjects()
 *
 * @method   static array fetchRelations($sql, $params = [])
 * @see      Instance::fetchRelations()
 *
 * @method   static bool transaction($process)
 * @see      Instance::transaction()
 *
 * @method   static void disconnect()
 * @see      Instance::disconnect()
 */
class Db
{
    use ProxyTrait;

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
