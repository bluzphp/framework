<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Tests\Fixtures\Db;

use Bluz\Db\Table;

/**
 * Concrete realization of Table class without row class specified.
 *
 * @category Tests
 * @package  Bluz\Db
 *
 * @author   Dmitriy Savchenko <login.was.here@gmail.com>
 */
class ConcreteTableWithoutRowClass extends Table
{
    protected string $name = 'foo';
    protected array $primary = ['bar', 'baz'];
}
