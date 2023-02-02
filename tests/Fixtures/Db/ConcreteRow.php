<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Tests\Fixtures\Db;

use Bluz\Db\Row;
use Bluz\Db\Traits\RowRelations;

/**
 * Concrete realization of Table class.
 *
 * @category Tests
 * @package  Bluz\Db
 *
 * @property mixed someValue
 *
 * @author   Eugene Zabolotniy <realbaziak@gmail.com>
 */
class ConcreteRow extends Row
{
    use RowRelations;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->setTable(ConcreteTable::getInstance());
    }
}
