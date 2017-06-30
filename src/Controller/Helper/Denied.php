<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Controller\Helper;

use Bluz\Application\Exception\ForbiddenException;
use Bluz\Controller\Controller;

/**
 * Denied helper can be declared inside Bootstrap
 *
 * @return void
 * @throws ForbiddenException
 */
return
    function () {
        /**
         * @var Controller $this
         */
        throw new ForbiddenException();
    };
