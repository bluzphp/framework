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

use Bluz\Application\Application;
use Bluz\Proxy\Auth;

return
    /**
     * Get current user
     * @var Application $this
     * @return \Bluz\Auth\AbstractRowEntity|null
     */
    function () {
        return Auth::getIdentity();
    };
