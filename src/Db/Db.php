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
namespace Bluz\Db;

use Bluz\Common\Exception\ConfigurationException;
use Bluz\Common\Options;
use Bluz\Db\Exception\DbException;
use Bluz\Db\Query;
use Bluz\Proxy\Logger;

/**
 * PDO wrapper
 *
 * @package  Bluz\Db
 * @author   Anton Shevchuk
 * @link     https://github.com/bluzphp/framework/wiki/Db
 */
class Db
{
    use Options;

    /**
     * PDO connection settings
     * @var  array
     * @link http://php.net/manual/en/pdo.construct.php
     */
    protected $connect = [
        "type" => "mysql",
        "host" => "localhost",
        "name" => "",
        "user" => "root",
        "pass" => "",
        "options" => []
    ];

    /**
     * PDO connection flags
     * @var  array
     * @link http://php.net/manual/en/pdo.setattribute.php
     */
    protected $attributes = [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
    ];

    /**
     * @var \PDO PDO instance
     */
    protected $handler;

    /**
     * Setup connection
     *
     * Just save connection settings
     * <code>
     *     $db->setConnect([
     *         'type' => 'mysql',
     *         'host' => 'localhost',
     *         'name' => 'db name',
     *         'user' => 'root',
     *         'pass' => ''
     *     ]);
     * </code>
     *
     * @param  array $connect options
     * @return Db
     * @throws DbException
     */
    public function setConnect(array $connect)
    {
        $this->connect = array_merge($this->connect, $connect);
        $this->checkConnect();
        return $this;
    }

    /**
     * Check connection options
     *
     * @return void
     * @throws ConfigurationException
     */
    private function checkConnect()
    {
        if (empty($this->connect['type']) or
            empty($this->connect['host']) or
            empty($this->connect['name']) or
            empty($this->connect['user'])
        ) {
            throw new ConfigurationException(
                'Database adapter is not configured.
                Please check `db` configuration section: required type, host, db name and user'
            );
        }
    }

    /**
     * Setup attributes for PDO connect
     *
     * @param  array $attributes
     * @return Db
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * Connect to Db
     *
     * @return Db
     * @throws DbException
     */
    public function connect()
    {
        if (empty($this->handler)) {
            try {
                $this->checkConnect();
                $this->log("Connect to " . $this->connect['host']);
                $this->handler = new \PDO(
                    $this->connect['type'] . ":host=" . $this->connect['host'] . ";dbname=" . $this->connect['name'],
                    $this->connect['user'],
                    $this->connect['pass'],
                    $this->connect['options']
                );

                foreach ($this->attributes as $attribute => $value) {
                    $this->handler->setAttribute($attribute, $value);
                }

                $this->ok();
            } catch (\Exception $e) {
                throw new DbException("Attempt connection to database is failed: {$e->getMessage()}");
            }
        }
        return $this;
    }

    /**
     * Return PDO handler
     *
     * @return \PDO
     */
    public function handler()
    {
        if (empty($this->handler)) {
            $this->connect();
        }
        return $this->handler;
    }

    /**
     * Prepare SQL query and return PDO Statement
     *
     * @param  string $sql    SQL query with placeholders
     * @param  array  $params params for query placeholders
     * @return \PDOStatement
     */
    protected function prepare($sql, $params)
    {
        $stmt = $this->handler()->prepare($sql);
        $stmt->execute($params);

        $this->log($sql, $params);

        return $stmt;
    }

    /**
     * Quotes a string for use in a query
     *
     * Example of usage
     * <code>
     *     $db->quote($_GET['id'])
     * </code>
     *
     * @param  string $value
     * @return string
     */
    public function quote($value)
    {
        return $this->handler()->quote($value);
    }

    /**
     * Quote a string so it can be safely used as a table or column name
     *
     * @param  string $identifier
     * @return string
     */
    public function quoteIdentifier($identifier)
    {
        // switch statement for DB type
        switch ($this->connect['type']) {
            case 'mysql':
                return '`' . str_replace('`', '``', $identifier) . '`';
            case 'postgresql':
            case 'sqlite':
            default:
                return '"' . str_replace('"', '\\' . '"', $identifier) . '"';
        }
    }

    /**
     * Execute SQL query
     *
     * Example of usage
     * <code>
     *     $db->query("SET NAMES 'utf8'");
     * </code>
     *
     * @param  string $sql    SQL query with placeholders
     *                        "UPDATE users SET name = :name WHERE id = :id"
     * @param  array  $params params for query placeholders (optional)
     *                        array (':name' => 'John', ':id' => '123')
     * @param  array  $types  Types of params (optional)
     *                        array (':name' => \PDO::PARAM_STR, ':id' => \PDO::PARAM_INT)
     * @return integer the number of rows
     */
    public function query($sql, $params = [], $types = [])
    {
        $stmt = $this->handler()->prepare($sql);
        foreach ($params as $key => &$param) {
            $stmt->bindParam(
                (is_int($key)?$key+1:":".$key),
                $param,
                $types[$key] ?? \PDO::PARAM_STR
            );
        }
        $this->log($sql, $params);
        $stmt->execute($params);
        $this->ok();
        return $stmt->rowCount();
    }

    /**
     * Create new query select builder
     *
     * @param array|string ...$select The selection expressions
     * @return Query\Select
     */
    public function select(...$select)
    {
        $query = new Query\Select();
        $query->select(...$select);
        return $query;
    }

    /**
     * Create new query insert builder
     *
     * @param  string $table
     * @return Query\Insert
     */
    public function insert($table)
    {
        $query = new Query\Insert();
        $query->insert($table);
        return $query;
    }

    /**
     * Create new query update builder
     *
     * @param  string $table
     * @return Query\Update
     */
    public function update($table)
    {
        $query = new Query\Update();
        $query->update($table);
        return $query;
    }

    /**
     * Create new query update builder
     *
     * @param  string $table
     * @return Query\Delete
     */
    public function delete($table)
    {
        $query = new Query\Delete();
        $query->delete($table);
        return $query;
    }

    /**
     * Return first field from first element from the result set
     *
     * Example of usage
     * <code>
     *     $db->fetchOne("SELECT COUNT(*) FROM users");
     * </code>
     *
     * @param  string $sql     SQL query with placeholders
     *                         "SELECT * FROM users WHERE name = :name AND pass = :pass"
     * @param  array  $params  params for query placeholders (optional)
     *                         array (':name' => 'John', ':pass' => '123456')
     * @return string
     */
    public function fetchOne($sql, $params = [])
    {
        $stmt = $this->prepare($sql, $params);
        $result = $stmt->fetch(\PDO::FETCH_COLUMN);

        $this->ok();
        return $result;
    }

    /**
     * Returns an array containing first row from the result set
     *
     * Example of usage
     * <code>
     *     $db->fetchRow("SELECT name, email FROM users WHERE id = ". $db->quote($id));
     *     $db->fetchRow("SELECT name, email FROM users WHERE id = ?", [$id]);
     *     $db->fetchRow("SELECT name, email FROM users WHERE id = :id", [':id'=>$id]);
     * </code>
     *
     * @param  string $sql     SQL query with placeholders
     *                         "SELECT * FROM users WHERE name = :name AND pass = :pass"
     * @param  array  $params  params for query placeholders (optional)
     *                         array (':name' => 'John', ':pass' => '123456')
     * @return array           array ('name' => 'John', 'email' => 'john@smith.com')
     */
    public function fetchRow($sql, $params = [])
    {
        $stmt = $this->prepare($sql, $params);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->ok();
        return $result;
    }

    /**
     * Returns an array containing all of the result set rows
     *
     * Example of usage
     * <code>
     *     $db->fetchAll("SELECT * FROM users WHERE ip = ?", ['192.168.1.1']);
     * </code>
     *
     * @param  string $sql    SQL query with placeholders
     *                        "SELECT * FROM users WHERE ip = :ip"
     * @param  array  $params params for query placeholders (optional)
     *                        array (':ip' => '127.0.0.1')
     * @return array[]
     */
    public function fetchAll($sql, $params = [])
    {
        $stmt = $this->prepare($sql, $params);
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->ok();
        return $result;
    }

    /**
     * Returns an array containing one column from the result set rows
     *
     * @param  string $sql    SQL query with placeholders
     *                        "SELECT id FROM users WHERE ip = :ip"
     * @param  array  $params params for query placeholders (optional)
     *                        array (':ip' => '127.0.0.1')
     * @return array
     */
    public function fetchColumn($sql, $params = [])
    {
        $stmt = $this->prepare($sql, $params);
        $result = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        $this->ok();
        return $result;
    }

    /**
     * Returns an array containing all of the result set rows
     *
     * Group by first column
     *
     * <code>
     *     $db->fetchGroup("SELECT ip, COUNT(id) FROM users GROUP BY ip", []);
     * </code>
     *
     * @param  string $sql    SQL query with placeholders
     *                        "SELECT ip, id FROM users"
     * @param  array  $params params for query placeholders (optional)
     * @param  mixed  $object
     * @return array
     */
    public function fetchGroup($sql, $params = [], $object = null)
    {
        $stmt = $this->prepare($sql, $params);

        if ($object) {
            $result = $stmt->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_GROUP, $object);
        } else {
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC | \PDO::FETCH_GROUP);
        }

        $this->ok();
        return $result;
    }

    /**
     * Returns an array containing all of the result set rows
     *
     * Group by first column
     *
     * @param  string $sql    SQL query with placeholders
     *                        "SELECT ip, id FROM users"
     * @param  array  $params params for query placeholders (optional)
     * @return array
     */
    public function fetchColumnGroup($sql, $params = [])
    {
        $stmt = $this->prepare($sql, $params);
        $result = $stmt->fetchAll(\PDO::FETCH_COLUMN | \PDO::FETCH_GROUP);

        $this->ok();
        return $result;
    }

    /**
     * Returns a key-value array
     *
     * @param  string $sql    SQL query with placeholders
     *                        "SELECT id, username FROM users WHERE ip = :ip"
     * @param  array  $params params for query placeholders (optional)
     *                        array (':ip' => '127.0.0.1')
     * @return array
     */
    public function fetchPairs($sql, $params = [])
    {
        $stmt = $this->prepare($sql, $params);
        $result = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);

        $this->ok();
        return $result;
    }

    /**
     * Returns an object containing first row from the result set
     *
     * Example of usage
     * <code>
     *     // Fetch object to stdClass
     *     $stdClass = $db->fetchObject('SELECT * FROM some_table WHERE id = ?', [$id]);
     *     // Fetch object to new Some object
     *     $someClass = $db->fetchObject('SELECT * FROM some_table WHERE id = ?', [$id], 'Some');
     *     // Fetch object to exists instance of Some object
     *     $someClass = $db->fetchObject('SELECT * FROM some_table WHERE id = ?', [$id], $someClass);
     * </code>
     *
     * @param  string $sql    SQL query with placeholders
     *                        "SELECT * FROM users WHERE name = :name AND pass = :pass"
     * @param  array  $params params for query placeholders (optional)
     *                        array (':name' => 'John', ':pass' => '123456')
     * @param  mixed  $object
     * @return array
     */
    public function fetchObject($sql, $params = [], $object = 'stdClass')
    {
        $stmt = $this->prepare($sql, $params);

        if (is_string($object)) {
            // some class name
            $result = $stmt->fetchObject($object);
        } else {
            // some instance
            $stmt->setFetchMode(\PDO::FETCH_INTO, $object);
            $result = $stmt->fetch(\PDO::FETCH_INTO);
        }

        $stmt->closeCursor();
        $this->ok();
        return $result;
    }

    /**
     * Returns an array of objects containing the result set
     *
     * @param  string $sql    SQL query with placeholders
     *                        "SELECT * FROM users WHERE name = :name AND pass = :pass"
     * @param  array  $params params for query placeholders (optional)
     *                        array (':name' => 'John', ':pass' => '123456')
     * @param  mixed  $object Class name or instance
     * @return array
     */
    public function fetchObjects($sql, $params = [], $object = null)
    {
        $stmt = $this->prepare($sql, $params);

        if (is_string($object)) {
            // fetch to some class by name
            $result = $stmt->fetchAll(\PDO::FETCH_CLASS, $object);
        } else {
            // fetch to StdClass
            $result = $stmt->fetchAll(\PDO::FETCH_OBJ);
        }

        $stmt->closeCursor();
        $this->ok();
        return $result;
    }

    /**
     * Returns an array of linked objects containing the result set
     *
     * @param  string $sql    SQL query with placeholders
     *                        "SELECT '__users', u.*, '__users_profile', up.*
     *                        FROM users u
     *                        LEFT JOIN users_profile up ON up.userId = u.id
     *                        WHERE u.name = :name"
     * @param  array  $params params for query placeholders (optional)
     *                        array (':name' => 'John')
     * @return array
     */
    public function fetchRelations($sql, $params = [])
    {
        $stmt = $this->prepare($sql, $params);

        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // prepare results
        $result = Relations::fetch($result);

        $stmt->closeCursor();
        $this->ok();
        return $result;
    }

    /**
     * Transaction wrapper
     *
     * Example of usage
     * <code>
     *     $db->transaction(function() use ($db) {
     *         $db->query("INSERT INTO `table` ...");
     *         $db->query("UPDATE `table` ...");
     *         $db->query("DELETE FROM `table` ...");
     *     })
     * </code>
     *
     * @param  callable $process callable structure - closure function or class with __invoke() method
     * @return bool
     * @throws DbException
     */
    public function transaction($process)
    {
        if (!is_callable($process)) {
            throw new DbException('First argument of transaction method should be callable');
        }
        try {
            $this->handler()->beginTransaction();
            call_user_func($process);
            $this->handler()->commit();
            return true;
        } catch (\PDOException $e) {
            $this->handler()->rollBack();
            return false;
        }
    }

    /**
     * Setup timer
     *
     * @return void
     */
    protected function ok()
    {
        Logger::info("<<<");
    }

    /**
     * Log queries by Application
     *
     * @param  string $sql     SQL query for logs
     * @param  array  $context
     * @return void
     */
    protected function log($sql, array $context = [])
    {
        $sql = str_replace('%', '%%', $sql);
        $sql = preg_replace('/\?/', '"%s"', $sql, sizeof($context));

        // replace mask by data
        $log = vsprintf($sql, $context);

        Logger::info($log);
    }

    /**
     * Disconnect PDO and clean default adapter
     *
     * @return void
     */
    public function disconnect()
    {
        if ($this->handler) {
            $this->handler = null;
        }
    }
}
