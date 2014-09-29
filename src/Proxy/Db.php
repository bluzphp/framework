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
 * @package  Bluz\Proxy
 *
 * @method   static Instance getInstance()
 *
 * @method   static string quote($value)
 * @method   static string quoteIdentifier($identifier)
 * @method   static integer query($sql, $params = array(), $types = array())
 * @method   static Query\Select select($select)
 * @method   static Query\Insert insert($table)
 * @method   static Query\Update update($table)
 * @method   static Query\Delete delete($table)
 * @method   static string fetchOne($sql, $params = array())
 * @method   static array fetchRow($sql, $params = array())
 * @method   static array fetchAll($sql, $params = array())
 * @method   static array fetchColumn($sql, $params = array())
 * @method   static array fetchGroup($sql, $params = array())
 * @method   static array fetchColumnGroup($sql, $params = array())
 * @method   static array fetchPairs($sql, $params = array())
 * @method   static array fetchObject($sql, $params = array(), $object = "stdClass")
 * @method   static array fetchObjects($sql, $params = array(), $object = null)
 * @method   static array fetchRelations($sql, $params = array())
 * @method   static bool transaction($process)
 * @method   static void disconnect()
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
