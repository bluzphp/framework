<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Db\Query\Traits;

use Bluz\Db\Query\Delete;
use Bluz\Db\Query\Select;

/**
 * From Trait
 *
 * Required for:
 *  - Select Builder
 *  - Delete Builder
 *
 * @package  Bluz\Db\Query\Traits
 * @author   Anton Shevchuk
 *
 * @property array $aliases
 * @method   Select|Delete addQueryPart(string $sqlPartName, mixed $sqlPart, $append = false)
 */
trait From
{
    /**
     * Set FROM
     *
     * Create and add a query root corresponding to the table identified by the
     * given alias, forming a cartesian product with any existing query roots
     *
     * <code>
     *     $sb = new SelectBuilder();
     *     $sb
     *         ->select('u.id')
     *         ->from('users', 'u')
     * </code>
     *
     * @param  string $from   The table
     * @param  string $alias  The alias of the table
     * @return $this
     */
    public function from($from, $alias)
    {
        $this->aliases[] = $alias;

        return $this->addQueryPart(
            'from',
            [
                'table' => $from,
                'alias' => $alias
            ],
            true
        );
    }
}
