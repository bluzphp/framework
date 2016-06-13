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

use Bluz\Proxy\Auth;

/**
 * Get current user
 *
 * @return \Bluz\Auth\AbstractRowEntity|null
 */
return
    function () {
        return Auth::getIdentity();
    };
