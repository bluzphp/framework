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
use Bluz\Http\StatusCode;
use Bluz\Proxy\Request;
use Bluz\Proxy\Response;

/**
 * Redirect helper can be declared inside Bootstrap
 *
 * @param string $url
 *
 * @return null
 */
return
    function ($url) {
        /**
         * @var Application $this
         */
        $this->useLayout(false);

        Response::removeHeaders();
        Response::clearBody();

        if (Request::isXmlHttpRequest()) {
            Response::setStatusCode(StatusCode::NO_CONTENT);
            Response::setHeader('Bluz-Redirect', (string)$url);
        } else {
            Response::setStatusCode(StatusCode::FOUND);
            Response::setHeader('Location', (string)$url);
        }

        return null;
    };
