<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Db;

use Bluz\Db\Exception\RelationNotFoundException;

/**
 * Relations map of Db tables
 *
 * @package  Bluz\Db
 * @author   Anton Shevchuk
 */
class Relations
{
    /**
     * Relation stack, i.e.
     * <code>
     *     [
     *         'Model1:Model2' => ['Model1'=>'foreignKey', 'Model2'=>'primaryKey'],
     *         'Pages:Users' => ['Pages'=>'userId', 'Users'=>'id'],
     *         'PagesTags:Pages' => ['PagesTags'=>'pageId', 'Pages'=>'id'],
     *         'PagesTags:Tags' => ['PagesTags'=>'tagId', 'Tags'=>'id'],
     *         'Pages:Tags' => ['PagesTags'],
     *     ]
     * </code>
     *
     * @var array
     */
    protected static $relations;

    /**
     * Class map, i.e.
     * <code>
     *     [
     *         'Pages' => '\Application\Pages\Table',
     *         'Users' => '\Application\Users\Table',
     *     ]
     * </code>
     *
     * @var array
     */
    protected static $modelClassMap;

    /**
     * Setup relation between two models
     *
     * @param  string $modelOne
     * @param  string $keyOne
     * @param  string $modelTwo
     * @param  string $keyTwo
     *
     * @return void
     */
    public static function setRelation($modelOne, $keyOne, $modelTwo, $keyTwo) : void
    {
        $relations = [$modelOne => $keyOne, $modelTwo => $keyTwo];
        self::setRelations($modelOne, $modelTwo, $relations);
    }

    /**
     * Setup multi relations
     *
     * @param  string $modelOne
     * @param  string $modelTwo
     * @param  array  $relations
     *
     * @return void
     */
    public static function setRelations($modelOne, $modelTwo, $relations) : void
    {
        $name = [$modelOne, $modelTwo];
        sort($name);
        $name = implode(':', $name);
        // create record in static variable
        self::$relations[$name] = $relations;
    }

    /**
     * Get relations
     *
     * @param  string $modelOne
     * @param  string $modelTwo
     *
     * @return array|false
     */
    public static function getRelations($modelOne, $modelTwo)
    {
        $name = [$modelOne, $modelTwo];
        sort($name);
        $name = implode(':', $name);

        return self::$relations[$name] ?? false;
    }

    /**
     * findRelation
     *
     * @param  Row    $row
     * @param  string $relation
     *
     * @return array
     * @throws Exception\TableNotFoundException
     * @throws Exception\RelationNotFoundException
     */
    public static function findRelation($row, $relation) : array
    {
        $model = $row->getTable()->getModel();

        /** @var \Bluz\Db\Table $relationTable */
        $relationTable = self::getModelClass($relation);
        $relationTable::getInstance();

        if (!$relations = self::getRelations($model, $relation)) {
            throw new RelationNotFoundException(
                "Relations between model `$model` and `$relation` is not defined"
            );
        }

        // check many-to-many relations
        if (count($relations) === 1) {
            $relations = Relations::getRelations($model, current($relations));
        }

        $field = $relations[$model];
        $key = $row->{$field};

        return self::findRelations($model, $relation, [$key]);
    }

    /**
     * Find Relations between two tables
     *
     * @param  string $modelOne Table
     * @param  string $modelTwo Target table
     * @param  array  $keys     Keys from first table
     *
     * @return array
     * @throws Exception\RelationNotFoundException
     */
    public static function findRelations($modelOne, $modelTwo, $keys) : array
    {
        $keys = (array)$keys;
        if (!$relations = self::getRelations($modelOne, $modelTwo)) {
            throw new RelationNotFoundException("Relations between model `$modelOne` and `$modelTwo` is not defined");
        }

        /* @var Table $tableOneClass name */
        $tableOneClass = self::getModelClass($modelOne);

        /* @var string $tableOneName */
        $tableOneName = $tableOneClass::getInstance()->getName();

        /* @var Table $tableTwoClass name */
        $tableTwoClass = self::getModelClass($modelTwo);

        /* @var string $tableTwoName */
        $tableTwoName = $tableTwoClass::getInstance()->getName();

        /* @var Query\Select $tableTwoSelect */
        $tableTwoSelect = $tableTwoClass::getInstance()::select();

        // check many to many relation
        if (\is_int(\array_keys($relations)[0])) {
            // many to many relation over third table
            $modelThree = $relations[0];

            // relations between target table and third table
            $relations = self::getRelations($modelTwo, $modelThree);

            /* @var Table $tableThreeClass name */
            $tableThreeClass = self::getModelClass($modelThree);

            /* @var string $tableTwoName */
            $tableThreeName = $tableThreeClass::getInstance()->getName();

            // join it to query
            $tableTwoSelect->join(
                $tableTwoName,
                $tableThreeName,
                $tableThreeName,
                $tableTwoName . '.' . $relations[$modelTwo] . '=' . $tableThreeName . '.' . $relations[$modelThree]
            );

            // relations between source table and third table
            $relations = self::getRelations($modelOne, $modelThree);

            // join it to query
            $tableTwoSelect->join(
                $tableThreeName,
                $tableOneName,
                $tableOneName,
                $tableThreeName . '.' . $relations[$modelThree] . '=' . $tableOneName . '.' . $relations[$modelOne]
            );

            // set source keys
            $tableTwoSelect->where($tableOneName . '.' . $relations[$modelOne] . ' IN (?)', $keys);
        } else {
            // set source keys
            $tableTwoSelect->where($relations[$modelTwo] . ' IN (?)', $keys);
        }
        return $tableTwoSelect->execute();
    }

    /**
     * Add information about model's classes
     *
     * @param  string $model
     * @param  string $className
     *
     * @return void
     */
    public static function addClassMap($model, $className) : void
    {
        self::$modelClassMap[$model] = $className;
    }

    /**
     * Get information about Model classes
     *
     * @param  string $model
     *
     * @return string
     * @throws Exception\RelationNotFoundException
     */
    public static function getModelClass($model) : string
    {
        if (!isset(self::$modelClassMap[$model])) {
            // try to detect
            $className = '\\Application\\' . $model . '\\Table';

            if (!class_exists($className)) {
                throw new RelationNotFoundException("Related class for model `$model` not found");
            }
            self::$modelClassMap[$model] = $className;
        }
        return self::$modelClassMap[$model];
    }

    /**
     * Get information about Table classes
     *
     * @param  string $modelName
     * @param  array  $data
     *
     * @return RowInterface
     * @throws Exception\RelationNotFoundException
     */
    public static function createRow($modelName, $data) : RowInterface
    {
        $tableClass = self::getModelClass($modelName);

        /* @var Table $tableClass name */
        return $tableClass::getInstance()::create($data);
    }

    /**
     * Fetch by Divider
     *
     * @param  array $input
     *
     * @return array
     * @throws Exception\RelationNotFoundException
     */
    public static function fetch($input) : array
    {
        $output = [];
        $map = [];
        foreach ($input as $i => $row) {
            $model = '';
            foreach ($row as $key => $value) {
                if (strpos($key, '__') === 0) {
                    $model = substr($key, 2);
                    continue;
                }
                $map[$i][$model][$key] = $value;
            }
            foreach ($map[$i] as $model => &$data) {
                $data = self::createRow($model, $data);
            }
            $output[] = $map;
        }
        return $output;
    }
}
