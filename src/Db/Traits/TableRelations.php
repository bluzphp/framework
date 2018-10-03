<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Db\Traits;

use Bluz\Db\Relations;

/**
 * TableRelations
 *
 * @package  Bluz\Db\Traits
 * @author   Anton Shevchuk
 */
trait TableRelations
{
    /**
     * Setup relation "one to one" or "one to many"
     *
     * @param  string $key
     * @param  string $model
     * @param  string $foreign
     *
     * @return void
     */
    public function linkTo($key, $model, $foreign): void
    {
        Relations::setRelation($this->model, $key, $model, $foreign);
    }

    /**
     * Setup relation "many to many"
     * [table1-key] [table1_key-table2-table3_key] [table3-key]
     *
     * @param  string $model
     * @param  string $link
     *
     * @return void
     */
    public function linkToMany($model, $link): void
    {
        Relations::setRelations($this->model, $model, [$link]);
    }
}
