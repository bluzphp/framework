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

use Bluz\Proxy\Request;
use Bluz\Proxy\Response;

/**
 * Reload helper can be declared inside Bootstrap
 */
return
    function () {
        Response::removeHeaders();
        Response::clearBody();

        if (Request::isXmlHttpRequest()) {
            Response::setStatusCode(204);
            Response::setHeader('Bluz-Reload', 'true');
        } else {
            Response::setStatusCode(302);
            Response::setHeader('Location', Request::getRequestUri());
        }
    };
