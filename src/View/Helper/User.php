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

return
    /**
     * Get current user
     *
     * @var View $this
     * @return \Bluz\Auth\AbstractRowEntity|null
     */
    function () {
    return app()->getAuth() ?
        app()->getAuth()->getIdentity() :
        null;
    };
