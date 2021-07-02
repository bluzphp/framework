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
use Bluz\Http\Exception\ForbiddenException;
use Bluz\Controller\Controller;

/**
 * Forbidden helper can be declared inside Bootstrap
 *
 * @param ForbiddenException $exception
 *
 * @return Controller
 */
return
    function (ForbiddenException $exception) {
        /**
         * @var Application $this
         */
        return $this->error($exception);
    };
