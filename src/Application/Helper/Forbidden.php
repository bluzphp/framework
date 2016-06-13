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

use Bluz\Application\Application;
use Bluz\Application\Exception\ForbiddenException;
use Bluz\Controller\Controller;

/**
 * Forbidden helper can be declared inside Bootstrap
 * @param ForbiddenException $exception
 * @return Controller
 */
return
    function ($exception) {
        /**
         * @var Application $this
         */
        return $this->error($exception);
    };
