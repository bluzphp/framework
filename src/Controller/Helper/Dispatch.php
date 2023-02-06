<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Controller\Helper;

use Bluz\Common\Exception\CommonException;
use Bluz\Common\Exception\ComponentException;
use Bluz\Controller\Controller;
use Bluz\Controller\ControllerException;
use Bluz\Proxy\Application;
use ReflectionException;

/**
 * Dispatch controller
 *
 * @param string $module
 * @param string $controller
 * @param array $params
 *
 * @return Controller
 * @throws CommonException
 * @throws ComponentException
 * @throws ControllerException
 * @throws ReflectionException
 */
return
    function (string $module, string $controller, array $params = []) {
        /**
         * @var Controller $this
         */
        return Application::getInstance()->dispatch($module, $controller, $params);
    };
