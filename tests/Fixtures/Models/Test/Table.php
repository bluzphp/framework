<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/skeleton
 */

/**
 * @namespace
 */

namespace Bluz\Tests\Fixtures\Models\Test;

/**
 * Table
 *
 * @package  Bluz\Tests\Fixtures
 *
 * @author   Anton Shevchuk
 * @created  08.07.11 17:36
 */
class Table extends \Bluz\Db\Table
{
    /**
     * Table
     *
     * @var string
     */
    protected string $name = 'test';

    /**
     * Primary key(s)
     *
     * @var array
     */
    protected array $primary = ['id'];

    /**
     * Class name
     *
     * @var string
     */
    protected string $rowClass = Row::class;
}
