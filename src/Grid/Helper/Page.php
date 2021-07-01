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
     * @param int $page
     *
     * @return string|null
     */
    function ($page = 1) {
        /**
         * @var Grid\Grid $this
         */
        if ($page < 1 || $page > $this->pages()) {
            return null;
        }

        return $this->getUrl(['page' => $page]);
    };
