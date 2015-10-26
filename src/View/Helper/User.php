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
namespace Bluz\View\Helper;

use Bluz\View\View;
use Bluz\Proxy\Auth;

return
    /**
     * Get current user
     *
     * @return \Bluz\Auth\AbstractRowEntity|null
     */
    function () {
        return Auth::getIdentity();
    };
