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
    function () {
    /**
     * @var Grid\Grid $this
     */
    if ($this->getPage() <= 1) {
        return null;
    }

    return $this->getUrl(['page' => $this->getPage() - 1]);
    };
