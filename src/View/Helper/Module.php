<?php
/**
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
     * @param string $module
     * @return string|boolean
     */
    function ($module = null) {
    /** @var View $this */
    $request = app()->getRequest();
    if (null == $module) {
        return $request->getModule();
    } else {
        return $request->getModule() == $module;
    }
    };
