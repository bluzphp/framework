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
     * @return integer
     * @throws Grid\GridException
     */
    function () {
        /**
         * @var Grid\Grid $this
         */
        return $this->getData()->getTotal();
    };
