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

use Bluz\Application\Exception\ForbiddenException;

return
    /**
     * Denied helper can be declared inside Bootstrap
     * @return bool
     */
    function () {
        throw new ForbiddenException();
    };
