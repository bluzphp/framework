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
use Bluz\Controller\Controller;

/**
 * Dispatch controller
 *
 * @param $module
 * @param $controller
 * @param array $params
 * @return Controller
 */
return
    function ($module, $controller, $params = []) {
        /**
         * @var Controller $this
         */
        return Application::getInstance()->dispatch($module, $controller, $params);
    };
