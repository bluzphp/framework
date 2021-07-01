<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Controller\Helper;

use Bluz\Application\Application;
use Bluz\Controller\Controller;

/**
 * Dispatch controller
 *
 * @param string $module
 * @param string $controller
 * @param array  $params
 *
 * @return Controller
 */
return
    function ($module, $controller, $params = []) {
        /**
         * @var Controller $this
         */
        return Application::getInstance()->dispatch($module, $controller, $params);
    };
