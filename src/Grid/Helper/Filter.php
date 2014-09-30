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

use Bluz\Grid;

return
    /**
     * @return string|null $url
     */
    function ($column, $filter, $value, $reset = true) {
        /**
         * @var Grid\Grid $this
         */
        if (!in_array($column, $this->getAllowFilters()) &&
            !array_key_exists($column, $this->getAllowFilters())
        ) {
            return null;
        }
        if (!$this->checkFilter($filter)) {
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
