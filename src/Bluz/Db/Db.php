<?php
/**
 * Copyright (c) 2013 by Bluz PHP Team
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * @namespace
 */
namespace Bluz\Db;

/**
 * PDO wrapper
 *
 * @category Bluz
 * @package  Db
 *
 * @link https://github.com/AntonShevchuk/Bluz/wiki/Db
 *
 * <pre>
 * <code>
 * // init settings, not connect!
 * $db->setConnect(array(
 *     'type' => 'mysql',
 *     'host' => 'localhost',
 *     'name' => 'db name',
 *     'user' => 'root',
 *     'pass' => ''
 * ));
 *
 * // quote variable
 * $db->quote($id)
 *
 * // query
 * $db->query("SET NAMES 'utf8'");
 *
 * // get one
 * $db->fetchOne("SELECT COUNT(*) FROM users");
 *
 * // get row
 * $db->fetchRow("SELECT * FROM users WHERE id = ". $db->quote($id));
 * $db->fetchRow("SELECT * FROM users WHERE id = ?", array($id));
 * $db->fetchRow("SELECT * FROM users WHERE id = :id", array(':id'=>$id));
 *
 * // get array
 * $db->fetchAll("SELECT * FROM users WHERE ip = ?", array($ip));
 *
 * // get pairs
 * $pairs = $db->fetchPairs("SELECT ip, COUNT(id) FROM users GROUP BY ip", array());
 *
 * // get object
 * // .. to stdClass
 * $stdClass = $db->fetchObject('SELECT * FROM some_table WHERE id = ?', array($id));
 * // .. to new Some object
 * $someClass = $db->fetchObject('SELECT * FROM some_table WHERE id = ?', array($id), 'Some');
 * // .. to exists instance of Some object
 * $someClass = $db->fetchObject('SELECT * FROM some_table WHERE id = ?', array($id), $someClass);
 * </code>
 * </pre>
 *
 * @author   Anton Shevchuk
 * @created  07.07.11 15:36
 */
class Db
{
    use \Bluz\Package;

    /**
     * @var array
     */
    protected $connect = array(
        "type" => "mysql",
        "host" => "localhost",
        "name" => "",
        "user" => "root",
        "pass" => "",
    );

    /**
     * Error mode for PDO adapter
     * @link http://php.net/manual/en/pdo.setattribute.php
     * @var int
     */
    protected $errorMode = \PDO::ERRMODE_EXCEPTION;

    /**
     * @var \PDO
     */
    protected $dbh;

    /**
     * @var array
     */
    protected $queries;

    /**
     * Himself instance
     * @var Db
     */
    protected static $adapter;

    /**
     * setDefaultAdapter
     *
     * @param bool $flag
     * @return Db
     */
    public function setDefaultAdapter($flag = true)
    {
        if ($flag) {
            self::$adapter = $this;
        }
        return $this;
    }

    /**
     * getDefaultAdapter
     *
     * @throws DbException
     * @return Db
     */
    public static function getDefaultAdapter()
    {
        // check default adapter
        if (self::$adapter) {
            return self::$adapter;
        } else {
            throw new DbException("Default database adapter is not configured");
        }
    }

    /**
     * set error mode
     *
     * @param $mode
     * @return Db
     */
    public function setErrorMode($mode)
    {
        $this->errorMode = (int) $mode;
        return $this;
    }

    /**
     * setConnect
     *
     * @param array $connect options
     * @throws DbException
     * @return Db
     */
    public function setConnect($connect)
    {
        $this->connect = array_merge($this->connect, $connect);

        if (empty($this->connect['type']) or
            empty($this->connect['host']) or
            empty($this->connect['name']) or
            empty($this->connect['user'])
        ) {
            throw new DbException('Db connection can\'t be initialized: required type, host, db name and user');
        }
        return $this;
    }

    /**
     * connect to Db
     *
     * @throws DbException
     * @return Db
     */
    public function connect()
    {
        if (empty($this->dbh)) {
            if (empty($this->connect['type']) or
                empty($this->connect['host']) or
                empty($this->connect['name']) or
                empty($this->connect['user'])
            ) {
                throw new DbException('Db connection can\'t be initialized: required type, host, db name and user');
            }
            try {
                $this->log("Connect to " . $this->connect['host']);
                $this->dbh = new \PDO(
                    $this->connect['type'] . ":host=" . $this->connect['host'] . ";dbname=" . $this->connect['name'],
                    $this->connect['user'],
                    $this->connect['pass']
                );

                $this->dbh->setAttribute(\PDO::ATTR_ERRMODE, $this->errorMode);
                $this->log("Connected");
            } catch (\Exception $e) {
                throw new DbException('Attempt connection to database is failed');
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
        if (empty($this->dbh)) {
            $this->connect();
        }
        return $this->dbh;
    }

    /**
     * prepare SQL query
     *
     * @param string $sql
     * @param array $params
     * @param array $types
     * @return \PDOStatement
     */
    protected function prepare($sql, $params = array(), $types = array())
    {
        $this->log("Prepare query");
        $stmt = $this->handler()->prepare($sql);

        foreach ($params as $key => $param) {
            $stmt->bindParam(
                is_int($key)?$key+1:$key,
                $param,
                isset($types[$key])?$types[$key]:\PDO::PARAM_STR
            );
        }
        return $stmt;
    }

    /**
     * prepare array of params for SQL query
     *
     * @param array $params
     * @return string
     */
    protected function prepareWhere($params)
    {
        if (is_string($params) && !empty($params)) {
            return ' WHERE ' . $params;
        }

        if (!sizeof($params)) {
            return '';
        }

        $whereSql = array();
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $values = array_map([$this, 'quote'], $value);
                $values = join(',', $values);
                $whereSql[] = "{$key} IN ({$values})";
            } elseif (is_null($value)) {
                $whereSql[] = "{$key} IS NULL";
            } elseif (stripos($key, ' like')) {
                $value = $this->quote($value);
                $whereSql[] = "{$key} LIKE ({$value})";
            } else {
                $value = $this->quote($value);
                $whereSql[] = "{$key} = {$value}";
            }
        }
        return ' WHERE ' . join(' AND ', $whereSql);
    }

    /**
     * quote SQL query
     *
     * @param string $value
     * @return string
     */
    public function quote($value)
    {
        return $this->handler()->quote($value);
    }

    /**
     * @param string $sql <p>
     *  "UPDATE users SET name = :name WHERE id = :id"
     *  </p>
     * @param array $params <p>
     *  array (':name' => 'John', ':id' => '123')
     * </p>
     * @param array $types <p>
     *  array (':name' => \PDO::PARAM_STR, ':id' => \PDO::PARAM_INT)
     * </p>
     * @return string
     */
    public function query($sql, $params = array(), $types = array())
    {
        $stmt = $this->prepare($sql, $params, $types);
        $this->log($sql, $params);
        return $stmt->execute();
    }

    /**
     * @param string $table
     * @param array $params <p>
     *  array (':name' => 'John', ':id' => '123')
     * </p>
     * @throws DbException
     * @return string
     */
    public function insert($table, $params = array())
    {
        $sql = "INSERT INTO `$table` SET `" . join('` = ?,`', array_keys($params)) . "` = ?";

        $stmt = $this->prepare($sql);

        // only data from $params
        $execParams = array_values($params);

        $result = $stmt->execute($execParams);

        $this->log($sql, $execParams);
        if ($result) {
            return $this->handler()->lastInsertId();
        } else {
            throw new DbException('Unable to insert: ' . join(' | ', $stmt->errorInfo()));
        }
    }

    /**
     * @param string $table
     * @param array $params <p>
     *                             array (':name' => 'John', ':id' => '123')
     * </p>
     * @param array|string $where  <p>
     *  "id = 123"
     *  // or
     *  ["id" => 123]
     * </p>
     * @throws DbException
     * @return string
     */
    public function update($table, $params = array(), $where = array())
    {
        $sqlWhere = $this->prepareWhere($where);

        $sql = "UPDATE `$table` SET `" . join('` = ?,`', array_keys($params)) . "` = ? " . $sqlWhere;

        $stmt = $this->prepare($sql);

        // only data from $params
        $execParams = array_values($params);

        $result = $stmt->execute($execParams);

        $this->log($sql, $execParams);
        if ($result) {
            return $result;
        } else {
            throw new DbException('Unable to update: ' . join(' | ', $stmt->errorInfo()));
        }
    }

    /**
     * @param string $table
     * @param array|string $where <p>
     *  "id = 123"
     *  // or
     *  ["id" => 123]
     * </p>
     * @return string
     */
    public function delete($table, $where = array())
    {
        $sqlWhere = $this->prepareWhere($where);

        $sql = "DELETE FROM `{$table}` {$sqlWhere}";

        $stmt = $this->prepare($sql);

        $result = $stmt->execute();

        $this->log($sql, array_values($where));

        return $result;
    }

    /**
     * @param string $sql <p>
     *  "SELECT id FROM users WHERE name = :name AND pass = :pass"
     *  </p>
     * @param array $params <p>
     *  array (':name' => 'John', ':pass' => '123456')
     * </p>
     * @return string
     */
    public function fetchOne($sql, $params = array())
    {
        $stmt = $this->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(\PDO::FETCH_COLUMN);

        $this->log($sql, $params);
        return $result;
    }

    /**
     * @param string $sql <p>
     *  "SELECT * FROM users WHERE name = :name AND pass = :pass"
     *  </p>
     * @param array $params <p>
     *  array (':name' => 'John', ':pass' => '123456')
     * </p>
     * @return array
     */
    public function fetchRow($sql, $params = array())
    {
        $stmt = $this->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->log($sql, $params);
        return $result;
    }

    /**
     * @param string $sql <p>
     *  "SELECT * FROM users WHERE ip = :ip"
     *  </p>
     * @param array $params <p>
     *  array (':ip' => '127.0.0.1')
     * </p>
     * @return array
     */
    public function fetchAll($sql, $params = array())
    {
        $stmt = $this->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->log($sql, $params);
        return $result;
    }

    /**
     * @param string $sql <p>
     *  "SELECT id FROM users WHERE ip = :ip"
     *  </p>
     * @param array $params <p>
     *  array (':ip' => '127.0.0.1')
     * </p>
     * @return array
     */
    public function fetchColumn($sql, $params = array())
    {
        $stmt = $this->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetchAll(\PDO::FETCH_COLUMN);

        $this->log($sql, $params);
        return $result;
    }

    /**
     * @param string $sql <p>
     *  "SELECT ip, id FROM users"
     *  </p>
     * @param array $params
     * @return array
     */
    public function fetchGroup($sql, $params = array())
    {
        $stmt = $this->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC | \PDO::FETCH_GROUP);

        $this->log($sql, $params);
        return $result;
    }

    /**
     * @param string $sql <p>
     *  "SELECT ip, id FROM users"
     *  </p>
     * @param array $params
     * @return array
     */
    public function fetchColumnGroup($sql, $params = array())
    {
        $stmt = $this->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetchAll(\PDO::FETCH_COLUMN | \PDO::FETCH_GROUP);

        $this->log($sql, $params);
        return $result;
    }

    /**
     * @param string $sql <p>
     *  "SELECT id, username FROM users WHERE ip = :ip"
     *  </p>
     * @param array $params <p>
     *  array (':ip' => '127.0.0.1')
     * </p>
     * @return array
     */
    public function fetchPairs($sql, $params = array())
    {
        $stmt = $this->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);

        $this->log($sql, $params);
        return $result;
    }

    /**
     * @param string $sql <p>
     *  "SELECT * FROM users WHERE name = :name AND pass = :pass"
     *  </p>
     * @param array $params <p>
     *  array (':name' => 'John', ':pass' => '123456')
     * </p>
     * @param mixed $object
     *
     * @internal param int $cache ttl of cache in minutes
     * @return array
     */
    public function fetchObject($sql, $params = array(), $object = "stdClass")
    {
        $stmt = $this->prepare($sql);
        $result = null;
        $stmt->execute($params);
        if (is_object($object)) {
            // some instance
            $stmt->setFetchMode(\PDO::FETCH_INTO, $object);
            $result = $stmt->fetch(\PDO::FETCH_INTO);
        } else {
            // some class name
            $result = $stmt->fetchObject($object);
        }

        $stmt->closeCursor();
        $this->log($sql, $params);
        return $result;
    }

    /**
     * @param string $sql <p>
     *  "SELECT * FROM users WHERE name = :name AND pass = :pass"
     *  </p>
     * @param array $params <p>
     *  array (':name' => 'John', ':pass' => '123456')
     * </p>
     * @param mixed $object
     * @return array
     */
    public function fetchObjects($sql, $params = array(), $object = null)
    {
        $stmt = $this->prepare($sql);
        $result = null;
        $stmt->execute($params);
        if (!$object) {
            // StdClass
            $result = $stmt->fetchAll(\PDO::FETCH_OBJ);
        } elseif (is_object($object)) {
            // some instance
            $result = $stmt->fetchAll(\PDO::FETCH_INTO, $object);
        } elseif (is_string($object)) {
            // some class name
            $result = $stmt->fetchAll(\PDO::FETCH_CLASS, $object);
        }
        $stmt->closeCursor();
        $this->log($sql, $params);
        return $result;
    }

    /**
     * transaction wrapper
     *
     * @param \closure $process
     * @throws DbException
     * @return boolean
     */
    public function transaction($process)
    {
        if (!is_callable($process)) {
            throw new DbException('First argument of transaction method should be callable');
        }
        try {
            $this->handler()->beginTransaction();
            $process();
            $this->handler()->commit();
            return true;
        } catch (\PDOException $e) {
            $this->handler()->rollBack();
            return false;
        }
    }

    /**
     * log
     *
     * @param string $sql
     * @param array $context
     * @return void
     */
    protected function log($sql, array $context = [])
    {
        $sql = str_replace('%', '%%', $sql);
        $sql = str_replace('?', '"%s"', $sql);

        // replace mask by data
        $sql = vsprintf($sql, $context);

        $this->getApplication()->log("DB >> " . $sql);
    }
}
