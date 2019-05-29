<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Application\Helper;

use Bluz\Application\Application;
use Bluz\Http\Exception\RedirectException;
use Bluz\Http\StatusCode;
use Bluz\Proxy\Request;
use Bluz\Proxy\Response;

/**
 * Redirect helper can be declared inside Bootstrap
 *
 * @param RedirectException $exception
 *
 * @return null
 */
return
    function ($exception) {
        /**
         * @var Application $this
         */
        $this->useLayout(false);

        Response::removeHeaders();
        Response::clearBody();

        if (Request::isXmlHttpRequest()) {
            Response::setStatusCode(StatusCode::NO_CONTENT);
            Response::setHeader('Bluz-Redirect', $exception->getUrl());
        } else {
            Response::setStatusCode(StatusCode::FOUND);
            Response::setHeader('Location', $exception->getUrl());
        }

        return null;
    };
