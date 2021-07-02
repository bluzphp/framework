<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Controller\Helper;

use Bluz\Controller\Controller;
use Bluz\Proxy\Acl;

/**
 * Check privilege
 *
 * @param string $privilege
 *
 * @return bool
 */
return
    function (string $privilege) {
        /**
         * @var Controller $this
         */
        return Acl::isAllowed($this->module, $privilege);
    };
