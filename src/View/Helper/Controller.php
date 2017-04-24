<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\View\Helper;

use Bluz\Proxy\Request;

/**
 * Return controller name
 * or check to current controller
 *
 * @param  string $controller
 * @return string|bool
 */
return
    function ($controller = null) {
        if (is_null($controller)) {
            return Request::getController();
        }
        return Request::getController() === $controller;
    };
