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
namespace Bluz\View\Helper;

use Bluz\Proxy\Request;

return
    /**
     * Return module name
     * or check to current module
     *
     * @param  string $module
     * @return string|bool
     */
    function ($module = null) {
        if (is_null($module)) {
            return Request::getModule();
        } else {
            return Request::getModule() == $module;
        }
    };
