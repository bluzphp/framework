<?php
/**
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
     * get current user
     *
     * @return boolean
     */
    function () {
        throw new ForbiddenException();
    };
