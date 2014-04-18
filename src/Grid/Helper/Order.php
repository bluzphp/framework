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
namespace Bluz\Grid\Helper;

use Bluz\Application\Application;
use Bluz\Grid;

return
    /**
     * @return string|null $url
     */
    function ($column, $order = null, $defaultOrder = Grid\Grid::ORDER_ASC, $reset = true) {
    /**
     * @var Grid\Grid $this
     */
    if (!in_array($column, $this->getAllowOrders())) {
        return null;
    }

    $orders = $this->getOrders();

    // change order
    if (null === $order) {
        if (isset($orders[$column])) {
            $order = ($orders[$column] == Grid\Grid::ORDER_ASC) ?
                Grid\Grid::ORDER_DESC : Grid\Grid::ORDER_ASC;
        } else {
            $order = $defaultOrder;
        }
    }

    // reset ot additional sort collumn
    if ($reset) {
        $rewrite = ['orders' => []];
    } else {
        $rewrite = ['orders' => $orders];
    }

    $rewrite['orders'][$column] = $order;

    return $this->getUrl($rewrite);
    };
