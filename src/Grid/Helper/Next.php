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
     * @return string|null
     */
    function () {
        /**
         * @var Grid\Grid $this
         */
        if ($this->getPage() >= $this->pages()) {
            return null;
        }

        return $this->getUrl(['page' => $this->getPage() + 1]);
    };
