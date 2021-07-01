<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Controller\Helper;

use Bluz\Http\Exception\ForbiddenException;
use Bluz\Controller\Controller;
use Bluz\Proxy\Acl;

/**
 * Denied helper can be declared inside Bootstrap
 *
 * @return void
 */
return
    function () {
        /**
         * @var Controller $this
         */
        $privilege = $this->getMeta()->getPrivilege();
        if ($privilege && !Acl::isAllowed($this->getModule(), $privilege)) {
            throw new ForbiddenException();
        }
    };
