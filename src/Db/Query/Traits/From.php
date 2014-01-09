<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Db\Query\Traits;

/**
 * From Trait, required for:
 *  - Select Builder
 *  - Delete Builder
 *
 * @category Bluz
 * @package  Db
 * @subpackage Query
 *
 * @author   Anton Shevchuk
 * @created  17.06.13 10:46
 */
trait From {

    /**
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
     * @param string $from   The table
     * @param string $alias  The alias of the table
     * @return $this
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
