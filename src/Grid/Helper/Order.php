<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Grid\Helper;

use Bluz\Grid;

return
    /**
     * @param string $column
     * @param null $order
     * @param string $defaultOrder
     * @param bool $reset
     *
     * @return string|null $url
     */
    function (string $column, $order = null, string $defaultOrder = Grid\Grid::ORDER_ASC, bool $reset = true) {
        /**
         * @var Grid\Grid $this
         */
        if (!$this->checkOrderColumn($column)) {
            return null;
        }

        $orders = $this->getOrders();

        // change order
        if (null === $order) {
            if (isset($orders[$column])) {
                $order = ($orders[$column] === Grid\Grid::ORDER_ASC) ?
                    Grid\Grid::ORDER_DESC : Grid\Grid::ORDER_ASC;
            } else {
                $order = $defaultOrder;
            }
        }

        // reset to additional sort column
        if ($reset) {
            $rewrite = ['orders' => []];
        } else {
            $rewrite = ['orders' => $orders];
        }

        $rewrite['orders'][$column] = $order;

        return $this->getUrl($rewrite);
    };
