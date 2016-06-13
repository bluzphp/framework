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

/**
 * Return module name
 * or check to current module
 *
 * @param  string $module
 * @return string|bool
 */
return
    function ($module = null) {
        if (is_null($module)) {
            return Request::getModule();
        } else {
            return Request::getModule() == $module;
        }
    };
