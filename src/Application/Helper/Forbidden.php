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

use Bluz\Application\Exception\ForbiddenException;
use Bluz\Controller\Controller;

/**
 * Forbidden helper can be declared inside Bootstrap
 * @param ForbiddenException $exception
 * @return Controller
 */
return
    function ($exception) {
        return $this->error($exception);
    };
