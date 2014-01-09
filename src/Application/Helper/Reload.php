<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Application\Helper;

use Bluz\Application\Exception\ReloadException;

return
    /**
     * reload current page
     * please, be careful to avoid loop of reload
     * @throws ReloadException
     * @return void
     */
    function () {
        throw new ReloadException();
    };
