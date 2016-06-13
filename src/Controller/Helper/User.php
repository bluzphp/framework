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

use Bluz\Controller\Controller;
use Bluz\Proxy\Auth;

/**
 * Get current user
 *
 * @return \Bluz\Auth\AbstractRowEntity|null
 */
return
    function () {
        /**
         * @var Controller $this
         */
        return Auth::getIdentity();
    };
