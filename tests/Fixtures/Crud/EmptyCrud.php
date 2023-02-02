<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/skeleton
 */

/**
 * @namespace
 */

namespace Bluz\Tests\Fixtures\Crud;

use Bluz\Crud\AbstractCrud;
use Bluz\Db\Traits\TableProperty;

/**
 * Crud based on Db\Table
 *
 * @package  Bluz\Tests\Fixtures
 *
 * @author   Anton Shevchuk
 * @created  03.09.12 13:11
 */
class EmptyCrud extends AbstractCrud
{
    use TableProperty;

    /**
     * @return array
     */
    public function getPrimaryKey(): array
    {
        return [];
    }
}
