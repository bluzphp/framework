<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Controller\Helper;

use Bluz\Auth\IdentityInterface;
use Bluz\Controller\Controller;
use Bluz\Proxy\Auth;

/**
 * Get current user
 *
 * @return IdentityInterface|null
 */
return
    function () {
        /**
         * @var Controller $this
         */
        return Auth::getIdentity();
    };
