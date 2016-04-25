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
 * Redirect helper can be declared inside Bootstrap
 * @param string $url
 */
return
    function ($url) {
        $this->useLayout(false);
        
        Response::removeHeaders();
        Response::clearBody();

        if (Request::isXmlHttpRequest()) {
            Response::setStatusCode(204);
            Response::setHeader('Bluz-Redirect', $url);
        } else {
            Response::setStatusCode(302);
            Response::setHeader('Location', $url);
        }
    };
