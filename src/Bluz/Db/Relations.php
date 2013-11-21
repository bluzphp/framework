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

use Bluz\Db\Exception\DbException;

/**
 * Relations map of Db tables
 *
 * @category Bluz
 * @package  Db
 *
 * @author   Anton Shevchuk
 * @created  12.11.13 13:22
 */
class Relations
{
    /**
     * <pre>
     * <code>
     * array(
     *     'table1:table2' => ['table1'=>'foreignKey', 'table2'=>'primaryKey'],
     *     'pages:users' => ['pages'=>'userId', 'users'=>'id'],
     *     'users:pages' => ['pages'=>'userId', 'users'=>'id'], // mirror
     *     'pages_tags:pages' => ['pages_tags'=>'pageId', 'pages'=>'id'],
     *     'pages_tags:tags' => ['pages_tags'=>'tagId', 'tags'=>'id'],
     *     'pages:tags' => ['pages_tags'],
     *     'tags:pages' => ['pages_tags'], // mirror
     * )
     * </code>
     * </pre>
     *
     * @var array
     */
    protected static $relations;

    /**
     * <pre>
     * <code>
     * array(
     *     'table_name' => '\Application\TableName\Table',
     *     'users' => '\Application\Users\Table',
     * )
     * </code>
     * </pre>
     *
     * @var array
     */
    protected static $classMap;

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
        // create record in static variable
        // record with mirror
        self::$relations[$tableOne .':'. $tableTwo] = static::$relations[$tableTwo .':'. $tableOne] = $relations;
    }

    /**
     * getRelations
     *
     * @param string $tableOne
     * @param string $tableTwo
     * @return array
     */
    public static function getRelations($tableOne, $tableTwo)
    {
        if (isset(self::$relations[$tableOne .':'. $tableTwo])) {
            return self::$relations[$tableOne .':'. $tableTwo];
        } else {
            return false;
        }
    }

    /**
     * Find Relations between two tables
     *
     * @param string $tableOne
     * @param string $tableTwo target table
     * @param array $keys from first table
     * @throws Exception\DbException
     * @return array
     */
    public static function findRelations($tableOne, $tableTwo, $keys)
    {
        $keys = (array) $keys;
        if (!$relations = self::getRelations($tableOne, $tableTwo)) {
            throw new DbException("Relations between table `$tableOne` and `$tableTwo` is not defined");
        }

        /* @var Table $tableTwoClass name */
        $tableTwoClass = self::$classMap[$tableTwo];
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
        self::$classMap[$tableName] = $className;
    }
}
 