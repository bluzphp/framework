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
namespace Bluz\Application\Helper;

use Bluz\Application\Application;
use Bluz\Controller\Controller;
use Bluz\Http\StatusCode;
use Bluz\Proxy\Response;
use Bluz\Proxy\Router;

/**
 * Reload helper can be declared inside Bootstrap
 * @param \Exception $exception
 * @return Controller
 */
return
    function ($exception) {
        /**
         * @var Application $this
         */
        Response::removeHeaders();
        Response::clearBody();

        // cast to valid HTTP error code
        // 500 - Internal Server Error
        $statusCode = (
                StatusCode::CONTINUE <= $exception->getCode()
                && $exception->getCode() <= StatusCode::INSUFFICIENT_STORAGE
            )
            ? $exception->getCode()
            : StatusCode::INTERNAL_SERVER_ERROR;
        Response::setStatusCode($statusCode);

        $module = Router::getErrorModule();
        $controller = Router::getErrorController();
        $params = ['code' => $exception->getCode(), 'message' => $exception->getMessage()];

        return $this->dispatch($module, $controller, $params);
    };
