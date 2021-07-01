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
     * @param int $limit
     *
     * @return string
     */
    function ($limit = 25) {
        /**
         * @var Grid\Grid $this
         */
        $rewrite = [];
        $rewrite['limit'] = (int)$limit;

        if ($limit !== $this->getLimit()) {
            $rewrite['page'] = 1;
        }

        return $this->getUrl($rewrite);
    };
