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
     * @return integer
     */
    function () {
        /**
         * @var Grid\Grid $this
         */
        return ceil($this->getData()->getTotal() / $this->getLimit());
    };
