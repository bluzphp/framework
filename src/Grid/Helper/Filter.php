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
     * @param string $filter
     * @param string $value
     * @param bool   $reset
     *
     * @return string|null $url
     */
    function ($column, $filter, $value, $reset = true) {
        /**
         * @var Grid\Grid $this
         */
        if (
            !$this->checkFilterName($filter) ||
            !$this->checkFilterColumn($column)
        ) {
            return null;
        }

        // reset filters
        if ($reset) {
            $rewrite = ['filters' => []];
        } else {
            $rewrite = ['filters' => $this->getFilters()];
        }

        $rewrite['filters'][$column][$filter] = $value;

        return $this->getUrl($rewrite);
    };
