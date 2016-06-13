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

use Bluz\Controller\Controller;
use Bluz\Proxy\Response;
use Bluz\Proxy\Router;

/**
 * Reload helper can be declared inside Bootstrap
 * @param \Exception $exception
 * @return Controller
 */
return
    function ($exception) {
        Response::removeHeaders();
        Response::clearBody();

        // cast to valid HTTP error code
        // 500 - Internal Server Error
        $statusCode = (100 <= $exception->getCode() && $exception->getCode() <= 505) ? $exception->getCode() : 500;
        Response::setStatusCode($statusCode);

        $module = Router::getErrorModule();
        $controller = Router::getErrorController();
        $params = ['code' => $exception->getCode(), 'message' => $exception->getMessage()];

        return $this->dispatch($module, $controller, $params);
    };
