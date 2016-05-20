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

use Bluz\Proxy\Acl;

return
    /**
     * Check privilege
     *
     * @return void
     */
    function ($privilege) {
        return Acl::isAllowed($this->module, $privilege);
    };
