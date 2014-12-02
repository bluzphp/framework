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

use Bluz\Db\Exception\RelationNotFoundException;

/**
 * Relations map of Db tables
 *
 * @package  Bluz\Db
 *
 * @author   Anton Shevchuk
 * @created  12.11.13 13:22
 */
class Relations
{
    /**
     * Relation stack, i.e.
     *     array(
     *         'table1:table2' => ['table1'=>'foreignKey', 'table2'=>'primaryKey'],
     *         'pages:users' => ['pages'=>'userId', 'users'=>'id'],
     *         'pages_tags:pages' => ['pages_tags'=>'pageId', 'pages'=>'id'],
     *         'pages_tags:tags' => ['pages_tags'=>'tagId', 'tags'=>'id'],
     *         'pages:tags' => ['pages_tags'],
     *     )
     *
     * @var array
     */
    protected static $relations;

    /**
     * Class map, i.e.
     *     array(
     *         'table_name' => '\Application\TableName\Table',
     *         'users' => '\Application\Users\Table',
     *     )
     *
     * @var array
     */
    protected static $tableClassMap;

    /**
     * Setup relation between two tables
     *
     * @param string $tableOne
     * @param string $keyOne
     * @param string $tableTwo
     * @param string $keyTwo
     * @return void
     */
    public static function setRelation($tableOne, $keyOne, $tableTwo, $keyTwo)
    {
        $relations = [$tableOne => $keyOne, $tableTwo => $keyTwo];
        self::setRelations($tableOne, $tableTwo, $relations);
    }

    /**
     * setMultiRelations
     *
     * @param string $tableOne
     * @param string $tableTwo
     * @param array $relations
     * @return void
     */
    public static function setRelations($tableOne, $tableTwo, $relations)
    {
        $name = [$tableOne, $tableTwo];
        sort($name);
        $name = join(':', $name);
        // create record in static variable
        self::$relations[$name] = $relations;
    }

    /**
     * getRelations
     *
     * @param string $tableOne
     * @param string $tableTwo
     * @return array|false
     */
    public static function getRelations($tableOne, $tableTwo)
    {
        $name = [$tableOne, $tableTwo];
        sort($name);
        $name = join(':', $name);

        if (isset(self::$relations[$name])) {
            return self::$relations[$name];
        } else {
            return false;
        }
    }

    /**
     * findRelation
     *
     * @param Row $row
     * @param string $tableRelation
     * @throws Exception\RelationNotFoundException
     * @return array
     */
    public static function findRelation($row, $tableRelation)
    {
        $tableRow = $row->getTable()->getName();

        if (!$relations = Relations::getRelations($tableRow, $tableRelation)) {
            throw new RelationNotFoundException(
                "Relations between table `$tableRow` and `$tableRelation` is not defined"
            );
        }

        // check many-to-many relations
        if (sizeof($relations) == 1) {
            $relations = Relations::getRelations($tableRow, current($relations));
        }

        $field = $relations[$tableRow];
        $key = $row->{$field};

        return Relations::findRelations($tableRow, $tableRelation, [$key]);
    }

    /**
     * Find Relations between two tables
     *
     * @param string $tableOne
     * @param string $tableTwo target table
     * @param array $keys from first table
     * @throws Exception\RelationNotFoundException
     * @return array
     */
    public static function findRelations($tableOne, $tableTwo, $keys)
    {
        $keys = (array) $keys;
        if (!$relations = self::getRelations($tableOne, $tableTwo)) {
            throw new RelationNotFoundException("Relations between table `$tableOne` and `$tableTwo` is not defined");
        }

        /* @var Table $tableTwoClass name */
        $tableTwoClass = self::getTableClass($tableTwo);
        /* @var Query\Select $tableTwoSelect */
        $tableTwoSelect = $tableTwoClass::getInstance()->select();

        // check many to many relation
        if (is_int(array_keys($relations)[0])) {
            // many to many relation over third table
            $tableThree = $relations[0];

            // relations between target table and third table
            $relations = self::getRelations($tableTwo, $tableThree);

            // join it to query
            $tableTwoSelect->join(
                $tableTwo,
                $tableThree,
                $tableThree,
                $tableTwo.'.'.$relations[$tableTwo].'='.$tableThree.'.'.$relations[$tableThree]
            );

            // relations between source table and third table
            $relations = self::getRelations($tableOne, $tableThree);

            // join it to query
            $tableTwoSelect->join(
                $tableThree,
                $tableOne,
                $tableOne,
                $tableThree.'.'.$relations[$tableThree].'='.$tableOne.'.'.$relations[$tableOne]
            );

            // set source keys
            $tableTwoSelect->where($tableOne.'.'. $relations[$tableOne] .' IN (?)', $keys);
        } else {
            // set source keys
            $tableTwoSelect->where($relations[$tableTwo] .' IN (?)', $keys);
        }
        return $tableTwoSelect->execute();
    }

    /**
     * Add information about Table classes
     *
     * @param string $tableName
     * @param string $className
     * @return void
     */
    public static function addClassMap($tableName, $className)
    {
        self::$tableClassMap[$tableName] = $className;
    }

    /**
     * Get information about Table classes
     *
     * @param string $tableName
     * @throws Exception\RelationNotFoundException
     * @return string
     */
    public static function getTableClass($tableName)
    {
        if (!isset(self::$tableClassMap[$tableName])) {
            // try to detect
            $modelName = ucwords(str_replace(['-', '_'], ' ', $tableName));
            $modelName = str_replace(' ', '', $modelName);
            $className = '\\Application\\'.$modelName.'\\Table';

            if (!class_exists($className)) {
                throw new RelationNotFoundException("Related class for table `$tableName` not found");
            }
            self::$tableClassMap[$tableName] = $className;
        }
        return self::$tableClassMap[$tableName];
    }

    /**
     * Get information about Table classes
     *
     * @param string $tableName
     * @param array $data
     * @throws Exception\RelationNotFoundException
     * @return Row
     */
    public static function createRow($tableName, $data)
    {
        $tableClass = self::getTableClass($tableName);

        /* @var Table $tableClass name */
        return $tableClass::getInstance()->create($data);
    }

    /**
     * Fetch by Divider
     *
     * @access  public
     * @param array $input
     * @return array
     */
    public static function fetch($input)
    {
        $output = array();
        $map = array();
        foreach ($input as $i => $row) {
            $table = '';
            foreach ($row as $key => $value) {
                if (strpos($key, '__') === 0) {
                    $table = substr($key, 2);
                    continue;
                }
                $map[$i][$table][$key] = $value;
            }
            foreach ($map[$i] as $table => &$data) {
                $data = self::createRow($table, $data);
            }
            $output[] = $map;
        }
        return $output;
    }
}
