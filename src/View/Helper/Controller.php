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
     * Return controller name
     * or check to current controller
     *
     * @param string $controller
     * @return string|boolean
     */
    function ($controller = null) {
    /** @var View $this */
    $request = app()->getRequest();
    if (null == $controller) {
        return $request->getController();
    } else {
        return $request->getController() == $controller;
    }
    };
