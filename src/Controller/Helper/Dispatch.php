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
namespace Bluz\Controller\Helper;

use Bluz\Application\Application;

return
    /**
     * Dispatch controller
     *
     * @return void
     */
    function ($module, $controller, $params = []) {
        return Application::getInstance()->dispatch($module, $controller, $params);
    };
