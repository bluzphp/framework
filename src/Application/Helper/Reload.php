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

use Bluz\Application\Exception\ReloadException;

return
    /**
     * Reload current page please, be careful to avoid loop of reload
     *
     * @return void
     * @throws ReloadException
     */
    function () {
        throw new ReloadException();
    };
