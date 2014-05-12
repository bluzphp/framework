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

use Bluz\View\View;

return
    /**
     * Return module name
     * or check to current module
     *
     * @var View $this
     * @param string $module
     * @return string|bool
     */
    function ($module = null) {
    $request = app()->getRequest();
    if (null == $module) {
        return $request->getModule();
    } else {
        return $request->getModule() == $module;
    }
    };
