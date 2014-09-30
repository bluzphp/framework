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
     * @return string
     */
    function ($limit = 25) {
        /**
         * @var Grid\Grid $this
         */
        $rewrite = array();
        $rewrite['limit'] = (int)$limit;

        if ($limit != $this->getLimit()) {
            $rewrite['page'] = 1;
        }

        return $this->getUrl($rewrite);
    };
