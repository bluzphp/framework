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
     * @return string|null
     */
    function ($page = 1) {
        /**
         * @var Grid\Grid $this
         */
        if ($page < 1 or $page > $this->pages()) {
            return null;
        }

        return $this->getUrl(['page' => $page]);
    };
