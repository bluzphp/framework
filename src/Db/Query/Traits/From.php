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
namespace Bluz\Db\Query\Traits;

use Bluz\Db\Query\Delete;
use Bluz\Db\Query\Select;

/**
 * From Trait, required for:
 *  - Select Builder
 *  - Delete Builder
 *
 * @package Bluz\Db\Query\Traits
 *
 * @property array $aliases
 * @method Select|Delete addQueryPart(string $sqlPartName, mixed $sqlPart, $append = false)
 *
 * @author   Anton Shevchuk
 * @created  17.06.13 10:46
 */
trait From
{
    /**
     * Set FROM
     *
     * Create and add a query root corresponding to the table identified by the
     * given alias, forming a cartesian product with any existing query roots
     *     $sb = new SelectBuilder();
     *     $sb
     *         ->select('u.id')
     *         ->from('users', 'u')
     *
     * @param string $from   The table
     * @param string $alias  The alias of the table
     * @return Select|Delete
     */
    public function from($from, $alias)
    {
        $this->aliases[] = $alias;

        return $this->addQueryPart(
            'from',
            array(
                'table' => $from,
                'alias' => $alias
            ),
            true
        );
    }
}
