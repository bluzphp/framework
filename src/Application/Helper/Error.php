<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

declare(strict_types=1);

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

        $module = Router::getErrorModule();
        $controller = Router::getErrorController();
        $params = ['code' => $exception->getCode(), 'exception' => $exception];

        return $this->dispatch($module, $controller, $params);
    };
